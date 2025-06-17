@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold">Tentang Kami</h1>
                <div class="border-bottom border-primary w-25 mx-auto my-4"></div>
            </div>
            
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body p-5">
                    <div class="about-content">
                        {!! $aboutContent ?? '<p class="lead">Portal Catering adalah platform yang menghubungkan pelanggan dengan penyedia jasa katering terbaik di kota Anda. Kami berkomitmen untuk menyediakan berbagai pilihan menu katering berkualitas untuk berbagai kebutuhan.</p>
                        <p>Didirikan pada tahun 2025, Portal Catering hadir sebagai solusi untuk mempermudah masyarakat dalam menemukan dan memesan layanan katering. Kami memahami betapa pentingnya makanan yang lezat dan berkualitas untuk acara atau kebutuhan sehari-hari Anda.</p>
                        <p>Kami bekerja sama dengan mitra katering terpercaya yang telah melalui proses seleksi ketat untuk memastikan kualitas dan kepuasan pelanggan. Dengan Portal Catering, Anda dapat dengan mudah menemukan menu katering yang sesuai dengan selera dan kebutuhan Anda.</p>
                        <p>Portal Catering terus berinovasi untuk meningkatkan pengalaman pengguna dalam memesan layanan katering. Kami berkomitmen untuk memberikan layanan terbaik dan memastikan setiap pesanan memenuhi harapan pelanggan.</p>
                        <h4 class="mt-4">Visi Kami</h4>
                        <p>Menjadi platform katering online terdepan yang menghubungkan pelanggan dengan penyedia jasa katering berkualitas.</p>
                        <h4 class="mt-4">Misi Kami</h4>
                        <ul>
                            <li>Menyediakan platform yang mudah digunakan untuk memesan layanan katering</li>
                            <li>Menjamin kualitas layanan mitra katering melalui proses seleksi ketat</li>
                            <li>Memberikan pilihan menu yang beragam sesuai kebutuhan pelanggan</li>
                            <li>Mendukung pertumbuhan bisnis mitra katering melalui platform digital</li>
                        </ul>' !!}
                    </div>
                </div>
            </div>
            
            <div class="text-center mb-5">
                <h2 class="fw-bold">Kontak Kami</h2>
                <div class="border-bottom border-primary w-25 mx-auto my-4"></div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <div class="row">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <h5 class="mb-3">Informasi Kontak</h5>
                            <address class="mb-0">
                                {!! $contactInfo ?? '<p><i class="fas fa-map-marker-alt me-2 text-primary"></i> Jl. Example No. 123, Kota Example</p>
                                <p><i class="fas fa-phone me-2 text-primary"></i> (021) 1234567</p>
                                <p><i class="fas fa-envelope me-2 text-primary"></i> info@portalcatering.com</p>
                                <p><i class="fas fa-clock me-2 text-primary"></i> Senin - Jumat: 08.00 - 17.00</p>' !!}
                            </address>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Kirim Pesan</h5>
                            <form>
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="Nama Lengkap">
                                </div>
                                <div class="mb-3">
                                    <input type="email" class="form-control" placeholder="Email">
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" rows="4" placeholder="Pesan"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection