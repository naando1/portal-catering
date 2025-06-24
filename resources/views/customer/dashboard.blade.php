@extends('layouts.main')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-3">
            @include('customer.partials.sidebar')
        </div>
        <div class="col-md-9">
            <h1 class="mb-4">Dashboard</h1>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">BMI</h5>
                            <h2 class="display-4">{{ number_format($healthMetrics['bmi'], 1) }}</h2>
                            <p class="text-muted">{{ $healthMetrics['bmi_category'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Target Kalori</h5>
                            <h2 class="display-4">{{ number_format($healthMetrics['target_calories']) }}</h2>
                            <p class="text-muted">kkal/hari</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Kondisi Kesehatan</h5>
                            @if(count($healthConditions) > 0)
                                @foreach($healthConditions as $condition)
                                    <span class="badge bg-info mb-1">{{ $condition }}</span>
                                @endforeach
                            @else
                                <p class="text-success">Tidak ada kondisi khusus</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Rekomendasi Menu</h5>
                        </div>
                        <div class="card-body">
                            <p>Berdasarkan profil kesehatan Anda, kami telah menyiapkan rekomendasi menu diet yang sesuai.</p>
                            <a href="{{ route('menus.diet') }}" class="btn btn-primary">Lihat Rekomendasi</a>
                            
                            @if($feedbackStats['total'] > 0)
                                <div class="mt-3">
                                    <p class="mb-1">Feedback Anda:</p>
                                    <div class="progress">
                                        @php
                                            $relevantPercentage = $feedbackStats['total'] > 0 ? 
                                                ($feedbackStats['relevant'] / $feedbackStats['total']) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $relevantPercentage }}%;" 
                                             aria-valuenow="{{ $relevantPercentage }}" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ round($relevantPercentage) }}% Relevan
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $feedbackStats['relevant'] }} dari {{ $feedbackStats['total'] }} rekomendasi sesuai</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Pesanan Terakhir</h5>
                        </div>
                        <div class="card-body">
                            @if($recentOrders->isEmpty())
                                <p class="text-muted">Anda belum memiliki pesanan.</p>
                            @else
                                <div class="list-group">
                                    @foreach($recentOrders as $order)
                                        <a href="{{ route('customer.orders.show', $order->id) }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Order #{{ $order->id }}</h6>
                                                <small>{{ $order->created_at->format('d M Y') }}</small>
                                            </div>
                                            <p class="mb-1">{{ $order->orderItems->count() }} item • Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                            <small class="text-muted">{{ ucfirst($order->status) }}</small>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
