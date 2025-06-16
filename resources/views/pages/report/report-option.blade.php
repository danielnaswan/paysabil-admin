@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
            <a href="{{ url('/report/participation') }}" style="text-decoration: none;">
                <div class="card card-frame" style="min-height: 250px;">
                    <div class="card-body p-7">
                        <div class="card-header p-0 pt-3 text-center"> 
                            <i class="fas fa-home text-primary text-gradient" style="font-size: 2.5rem;"></i>
                            <p class="text-md text-center mt-2">Click to go to</p>
                            <h4 class="mb-2 mt-2">Student Participation</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
            <a href="{{ url('/report/financial') }}" style="text-decoration: none;">
                <div class="card card-frame" style="min-height: 250px;">
                    <div class="card-body p-7">
                        <div class="card-header p-0 pt-3 text-center">
                            <i class="fas fa-chart-bar text-success text-gradient" style="font-size: 2.5rem;"></i>
                            <p class="text-md text-center mt-2">Click to go to</p>
                            <h4 class="mb-2 mt-2">Financial & Budget Tracking</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Second Row -->
        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
            <a href="{{ url('/report/anomaly') }}" style="text-decoration: none;">
                <div class="card card-frame" style="min-height: 250px;">
                    <div class="card-body p-7">
                        <div class="card-header p-0 pt-3 text-center">
                            <i class="fas fa-cog text-info text-gradient" style="font-size: 2.5rem;"></i>
                            <p class="text-md text-center mt-2">Click to go to</p>
                            <h4 class="mb-2 mt-2">Fraud & Anomaly</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-xl-6 col-sm-6">
            <a href="{{ url('/report/feedback') }}" style="text-decoration: none;">
                <div class="card card-frame" style="min-height: 250px;">
                    <div class="card-body p-7">
                        <div class="card-header p-0 pt-3 text-center">
                            <i class="fas fa-users text-warning text-gradient" style="font-size: 2.5rem;"></i>
                            <p class="text-md text-center mt-2">Click to go to</p>
                            <h4 class="mb-2 mt-2">Feedback & Complaint</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection