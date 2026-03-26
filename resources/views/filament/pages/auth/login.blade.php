<div class="ti-login-page">
    <div class="ti-login-grid">
        <aside class="ti-login-brand-panel">
            <div class="ti-login-brand-overlay"></div>
            <div class="ti-login-brand-content">
                <div class="ti-login-brand-main">
                    <div class="ti-login-brand-illustration-wrap">
                        <img
                            src="{{ asset('images/illustration-server.png') }}"
                            alt="Ilustrasi server infrastruktur TI"
                            class="ti-login-brand-illustration"
                            onerror="this.onerror=null;this.src='{{ asset('images/Icon for login.png') }}';"
                        >
                    </div>

                    <h1 class="ti-login-brand-title">Sistem Dokumentasi Infrastruktur TI</h1>
                    <p class="ti-login-brand-description">
                        Mendukung pencatatan pekerjaan, monitoring, dan pelaporan SKP secara terstruktur dan akuntabel.
                    </p>
                </div>

                <p class="ti-login-brand-copyright">
                    &copy; {{ now()->year }} TI Documentation. All rights reserved.
                </p>
            </div>
        </aside>

        <section class="ti-login-form-panel">
            <div class="ti-login-form-container">
                <div class="ti-login-form-card">
                    <div class="ti-login-form-head">
                        <h2 class="ti-login-form-title">Masuk ke Sistem</h2>
                        <p class="ti-login-form-subtitle">Silakan login menggunakan akun Anda</p>
                    </div>

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

                    <x-filament-panels::form id="form" wire:submit="authenticate">
                        {{ $this->form }}

                        <x-filament-panels::form.actions
                            :actions="$this->getCachedFormActions()"
                            :full-width="$this->hasFullWidthFormActions()"
                        />
                    </x-filament-panels::form>

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
                </div>
            </div>
        </section>
    </div>

    <x-filament-actions::modals />
</div>
