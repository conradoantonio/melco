@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-9 m-auto text-white p-t-40 p-b-90">
                    <h1><img class="dashboard-icon" style="border-radius: 50%; margin-top: -2mm; background: #4C5060;" src="{{asset('img/robot.png')}}">Robot XL-65.</h1>
                </div>
                <div class="col-md-3 m-auto text-white p-t-40 p-b-90">
                    <div class="text-center">
                        <p>
                            <button type="button" class="btn btn-sm m-r-30 btn-primary"> Editar</button>
                            <button type="button" class="btn btn-sm btn-success"> Iniciar streaming</button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container pull-up">
        <div class="row">
            <div class="col-lg-9 m-b-30">
                <div class="card shadow-lg">
                    <div class=" padding-box-2 p-all-25">
                        <div class="">
                            <h3 class="text-center">Información Robot</h3>
                            <div class="list-group">
                                <a href="#" class="list-group-item d-flex justify-content-between align-items-center active">
                                    <span><i class="mdi mdi-circle text-success"></i> All Vital Systems are operational</span>
                                </a>

                                <a href="#" class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="mdi mdi-circle text-success"></i> Database System</span>
                                    <span class="opacity-75">99.99%</span>
                                </a>

                                <a href="#" class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="mdi mdi-circle text-success"></i> File System</span>
                                    <span class="opacity-75">99.99%</span>
                                </a>

                                <a href="#" class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="mdi mdi-circle text-success"></i> Backup Servers</span>
                                    <span class="opacity-75">99.99%</span>
                                </a>

                                <a href="#" class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="mdi mdi-circle text-danger"></i> Content Delivery Networks 
                                        <span class="badge-dark badge">Under Review</span>
                                    </span>
                                    <span class="opacity-75">45%</span>
                                </a>

                                <a href="#" class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="mdi mdi-circle text-success"></i>  API v3</span>
                                    <span class="opacity-75">99.99%</span>
                                </a>
                                <a href="#" class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="mdi mdi-circle text-warning"></i>  API v1</span>
                                    <span class="opacity-75 ">  Going Offline 1 <sup>st</sup> Dec 2018</span>
                                </a>

                                <a href="#" class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="mdi mdi-circle text-success"></i>  Load Balancer</span>
                                    <span class="opacity-75">99.99%</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card shadow-lg">
                    <div class="">
                        <div class="p-t-30 text-center">
                            <h3 class="text-center">Especificaciones</h3>
                        </div>
                        <div class="rounded-bottom card-body text-left">
                            <ul>
                                <li><span style="font-weight: bold;">Lorem</span> &nbsp;&nbsp;&nbsp;Ipsum dolo</li>
                                <li><span style="font-weight: bold;">Lorem</span> &nbsp;&nbsp;&nbsp;Ipsum dolo</li>
                                <li><span style="font-weight: bold;">Lorem</span> &nbsp;&nbsp;&nbsp;Ipsum dolo</li>
                            </ul>
                            <div class="text-center">
                                <a href="#" class="btn btn-primary">Editar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

</script>
@endsection