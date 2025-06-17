<div class="list-group mb-4">
    <a href="{{ route('customer.profile.edit') }}" class="list-group-item list-group-item-action {{ request()->routeIs('customer.profile.edit') ? 'active' : '' }}">
        <i class="fas fa-user me-2"></i> Profil Saya
    </a>
    
    <a href="{{ route('customer.orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}">
        <i class="fas fa-shopping-bag me-2"></i> Pesanan Saya
    </a>
    
    <a href="{{ route('menus.diet') }}" class="list-group-item list-group-item-action {{ request()->routeIs('menus.diet') ? 'active' : '' }}">
        <i class="fas fa-apple-alt me-2"></i> Menu Diet
    </a>
</div>

{{-- Tambahkan ringkasan profil kesehatan jika sudah diisi --}}
@if(Auth::user()->hasCompleteHealthProfile())
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0"><i class="fas fa-clipboard-check me-2"></i> Status Kesehatan</h6>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <div>BMI:</div>
                <div class="fw-bold">{{ Auth::user()->getBmi() }}</div>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <div>Kategori:</div>
                <div class="fw-bold">{{ Auth::user()->getBmiCategory() }}</div>
            </div>
            @if(Auth::user()->dietPreference)
                <div class="d-flex justify-content-between mb-2">
                    <div>Jenis Diet:</div>
                    <div class="fw-bold">
                        @if(Auth::user()->dietPreference->diet_type == 'regular')
                            Diet Seimbang
                        @elseif(Auth::user()->dietPreference->diet_type == 'low_carb')
                            Rendah Karbohidrat
                        @elseif(Auth::user()->dietPreference->diet_type == 'low_fat')
                            Rendah Lemak
                        @elseif(Auth::user()->dietPreference->diet_type == 'low_sugar')
                            Rendah Gula
                        @elseif(Auth::user()->dietPreference->diet_type == 'low_sodium')
                            Rendah Garam
                        @elseif(Auth::user()->dietPreference->diet_type == 'high_protein')
                            Tinggi Protein
                        @elseif(Auth::user()->dietPreference->diet_type == 'vegetarian')
                            Vegetarian
                        @elseif(Auth::user()->dietPreference->diet_type == 'vegan')
                            Vegan
                        @endif
                    </div>
                </div>
            @endif
            <div class="mt-2">
                <a href="{{ route('customer.profile.edit', ['tab' => 'health']) }}" class="btn btn-sm btn-outline-success w-100">
                    <i class="fas fa-edit me-1"></i> Edit Profil Kesehatan
                </a>
            </div>
        </div>
    </div>
@else
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> Rekomendasi Diet</h6>
        </div>
        <div class="card-body">
            <p class="small mb-3">Lengkapi profil kesehatan Anda untuk mendapatkan rekomendasi menu diet yang sesuai dengan kondisi kesehatan Anda.</p>
            <a href="{{ route('customer.profile.edit', ['tab' => 'health']) }}" class="btn btn-sm btn-info w-100">
                <i class="fas fa-clipboard-list me-1"></i> Isi Profil Kesehatan
            </a>
        </div>
    </div>
@endif