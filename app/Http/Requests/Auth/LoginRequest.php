<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\SiakadApiService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('email') && ! $this->has('login')) {
            $this->merge(['login' => $this->input('email')]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginInput = trim((string) $this->input('login', $this->input('email')));
        $password = (string) $this->input('password');

        // 1. Check if user exists locally (by email or NIM)
        $localUser = User::where('email', $loginInput)
            ->orWhereHas('mahasiswa', function ($q) use ($loginInput) {
                $q->where('nim', $loginInput);
            })
            ->first();

        // 2. Primary: If user exists in local database, authenticate directly
        if ($localUser) {
            if (Auth::attempt(['email' => $localUser->email, 'password' => $password], $this->boolean('remember'))) {
                if (isset($localUser->status_aktif) && ! $localUser->status_aktif) {
                    Auth::logout();
                    RateLimiter::hit($this->throttleKey());
                    throw ValidationException::withMessages([
                        'login' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
                    ]);
                }

                RateLimiter::clear($this->throttleKey());

                return;
            }

            // If user is Super Admin or Admin Fakultas and password was wrong, reject immediately
            if ($localUser->isAdmin()) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'login' => 'Email atau kata sandi administrator tidak valid.',
                ]);
            }
        }

        // 3. For Mahasiswa, attempt authentication via SIAKAD Bridge API
        $siakadApi = app(SiakadApiService::class);
        $apiResponse = $siakadApi->login($loginInput, $password);

        if ($apiResponse['success']) {
            $token = $apiResponse['token'];
            $profileData = $apiResponse['data'] ?? [];

            // If profile data is minimal, fetch full profile using the Bearer token
            if ($token && (! isset($profileData['nama']) && ! isset($profileData['name']))) {
                $profileResponse = $siakadApi->getProfile($token);
                if ($profileResponse['success'] && ! empty($profileResponse['data'])) {
                    $profileData = array_merge($profileData, $profileResponse['data']);
                }
            }

            // Sync student data to local database
            $authenticatedUser = $siakadApi->syncStudent($loginInput, $profileData, $password);

            // Store Bearer token in session for subsequent API operations
            if ($token) {
                session(['siakad_token' => $token]);
            }

            Auth::login($authenticatedUser, $this->boolean('remember'));
            RateLimiter::clear($this->throttleKey());

            return;
        }

        // 4. Fallback: If SIAKAD returned network error AND local Mahasiswa user exists with matching password
        if ($apiResponse['is_network_error'] && $localUser && $localUser->isMahasiswa()) {
            if (Auth::attempt(['email' => $localUser->email, 'password' => $password], $this->boolean('remember'))) {
                RateLimiter::clear($this->throttleKey());
                session()->flash('warning', 'Masuk menggunakan data lokal karena koneksi ke Bridge API SIAKAD sedang tidak tersedia.');

                return;
            }
        }

        // 5. If authentication failed
        RateLimiter::hit($this->throttleKey());

        $errorMessage = filter_var($loginInput, FILTER_VALIDATE_EMAIL)
            ? 'Email atau kata sandi tidak sesuai.'
            : ($apiResponse['message'] ?? 'NIM atau password portal SIAKAD tidak valid.');

        throw ValidationException::withMessages([
            'login' => $errorMessage,
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $login = (string) $this->input('login', $this->input('email'));

        return Str::transliterate(Str::lower($login).'|'.$this->ip());
    }
}
