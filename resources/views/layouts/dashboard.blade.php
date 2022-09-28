@extends('layouts.main')

@section('content')
<style type="text/css">
    
</style>
<section class="admin-content">
    <div class="bg-dark ">
        <div class="container-fluid m-b-30">
            <div class="row">
                <div class="text-white col-lg-6">
                    <div class="p-all-15">
                        <div class="text-overline m-t-10 opacity-75">
                            Total de ingresos
                        </div>
                        <div class="d-md-flex m-t-20 align-items-center">
                            <div class="avatar avatar-lg my-auto mr-2">
                                <div class="avatar-title bg-warning rounded-circle">
                                    $
                                </div>
                            </div>
                            <h1 class="display-4">
                                {{number_format(5092787)}} MXN
                            </h1>
                            {{-- <h5 class=" text-danger ml-2"><i class="mdi mdi-arrow-down"></i> 31% Past 24hrs</h5> --}}
                        </div>
                        <p class="opacity-75">
                            Bienvenido sea {{auth()->user()->fullname}} al sistema administrativo {{env('APP_NAME')}}, aquí podrá gestionar todo el contenido relacionado a los datos de la aplicación, 
                            generar reportes de pedidos, gestionar productos y enviar notificaciones a los clientes de manera personalizada.
                        </p>
                    </div>
                </div>
                <div class="col-md-12 p-b-60">
                    <div class="chart">
                        <div id="apexchart-08" class="chart-canvas"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row d-none pull-up d-lg-flex">
            <div class="col m-b-30">
                <div class="card ">
                    <div class="card-body">
                        <div class="card-controls">
                            <a href="#" class="badge badge-soft-success"> <i class="mdi mdi-arrow-down"></i> 12 %</a>
                        </div>
                        <div class="text-center p-t-30 p-b-20">
                            <div class="text-overline text-muted opacity-75">Total de pedidos</div>
                            <h1 class="text-success">{{number_format(90)}}</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col m-b-30">
                <div class="card">
                    <div class="card-body">
                        <div class="card-controls">
                            <a href="#" class="badge badge-soft-info"> <i class="mdi mdi-arrow-down"></i> 35 %</a>
                        </div>
                        <div class="text-center p-t-30 p-b-20">
                            <div class="text-overline text-muted opacity-75">Pedidos finalizados</div>
                            <h1 class="text-info">{{number_format(60)}}</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col m-b-30">
                <div class="card ">
                    <div class="card-body">
                        <div class="card-controls">
                            <a href="#" class="badge badge-soft-success"> <i class="mdi mdi-arrow-up"></i> 32 %</a>
                        </div>
                        <div class="text-center p-t-30 p-b-20">
                            <div class="text-overline text-muted opacity-75">Pedidos de último mes</div>
                            <h1 class="text-success">{{number_format(50)}}</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col m-b-30">
                <div class="card ">
                    <div class="card-body">
                        <div class="card-controls">
                            <a href="#" class="badge badge-soft-info"> <i class="mdi mdi-arrow-down"></i> 10 %</a>
                        </div>
                        <div class="text-center p-t-30 p-b-20">
                            <div class="text-overline text-muted opacity-75">Total de usuarios</div>
                            <h1 class="text-info">{{number_format(205)}}</h1>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col m-b-30">
                <div class="card ">
                    <div class="card-body">
                        <div class="card-controls">
                            <a href="#" class="badge badge-soft-success"> <i class="mdi mdi-arrow-up"></i> 65 %</a>
                        </div>
                        <div class="text-center p-t-30 p-b-20">
                            <div class="text-overline text-muted opacity-75">
                                Total de productos
                            </div>
                            <h1 class="text-success">{{number_format(78)}}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12  m-b-30">
                <div class="card">
                    <div class="card-header">
                        {{-- <div class="card-title">Horario de ventas</div> --}}
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <div id="apexchart-07" class="chart-canvas"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 m-b-30">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h3>Top 10 clientes</h3>
                    </div>
                    <div class="col-md-12">
                        <div class="card m-b-30">
                            <div class="">
                                <div class="table-responsive">
                                    <table class="table table-borderless table-hover ">
                                        <thead>
                                        <tr>
                                            <th scope="col">Nombre cliente</th>
                                            <th scope="col">#Pedidos realizados</th>
                                            <th scope="col">Total comprado</th>
                                            <th scope="col">#Productos adquiridos</th>
                                            <th scope="col">Fecha de última compra</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="border-left border-strong border-success">José Rodolfo</td>
                                                <td>#2</td>
                                                <td>$ {{number_format(500000)}} MXN</td>
                                                <td>#32</td>
                                                <td><span class="text-info h5">14 de octubre del 2019</span></td>
                                            </tr>
                                            <tr>
                                                <td class="border-left border-strong border-success">Miguel Ángel</td>
                                                <td>#5</td>
                                                <td>$ {{number_format(60000)}} MXN</td>
                                                <td>#32</td>
                                                <td><span class="text-info h5">14 de octubre del 2019</span></td>
                                            </tr>
                                            <tr>
                                                <td class="border-left border-strong border-success">Edgard</td>
                                                <td>#10</td>
                                                <td>$ {{number_format(110000)}} MXN</td>
                                                <td>#90</td>
                                                <td><span class="text-info h5">14 de octubre del 2019</span></td>
                                            </tr>
                                            <tr>
                                                <td class="border-left border-strong border-success">Conrado</td>
                                                <td>#2</td>
                                                <td>$ {{number_format(53000)}} MXN</td>
                                                <td>#60</td>
                                                <td><span class="text-info h5">20 de Noviembre del 2019</span></td>
                                            </tr>
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-lg-4">
                <div class="col-md-12 m-b-10 text-center">
                    <h3>Productos más vendidos</h3>
                </div>
                <div class="card m-b-30">
                    <div class="card-body ">
                        @foreach($topProductos as $topProducto)
                            <div class="p-t-15 p-b-15  border-bottom border-bottom-dashed">
                                <div class="row ">
                                    <div class="col-md-7">
                                        <h6 class="">{{$topProducto->productosSupermarket->clave}}</h6>
                                        <p class="text-muted m-0 ">{{$topProducto->productosSupermarket->descripcion}}</p>
                                    </div>
                                    <div class="col-md-5 my-auto  text-right">
                                        <h4 class="text-primary m-0">{{$topProducto->coincidencias}} Vendido(s)</h4>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</section>
<script src="{{ asset('vendor/apexchart/apexcharts.min.js')}}"></script>
<script type="text/javascript">
    $(function() {
        function generateHeatMapData(count, yrange) {
            var i = 0;
            var series = [];
            while (i < count) {
                var x = (i + 1).toString();
                var y = Math.floor(Math.random() * (yrange.max - yrange.min + 1)) + yrange.min;

                series.push({
                    x: x,
                    y: y
                });
                i++;
            }
            return series;
        }

        'use strict';
        if ($("#apexchart-08").length) {
            var options = {
                colors: ["#687ae8","#12bfbb","#ffb058","#2991cf","#87b8d4","#109693","#f29494","#527cf9","#7140d1","#e79e4e","#52b4ee","#6ed7e0","#8fa6b4","#ffcfcf","#28304e","#95aac9","#f2545b","#f7bc06","#00cc99","#19b5fe","#E3EBF6"],
                chart: {
                    height: 350,
                    type: 'area',
                },
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    curve: 'smooth'
                },
                series: [{
                    name: 'Super market',
                    data: [50000,32111,60000,53000,32111,21333,53430]
                }, {
                    name: 'Fast food',
                    data: [60000,12111,20000,53433,15000,20000,23000]
                }],

                xaxis: {
                    categories: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
                    tickPlacement: 'between',
                },
                tooltip: {
                    fixed: {
                        enabled: false,
                        position: 'topRight'
                    }
                }
            }

            var chart = new ApexCharts(
                document.querySelector("#apexchart-08"),
                options
            );

            chart.render();
        }

        /*if ($("#apexchart-07").length) {
            var options = {
                chart: {
                    height: 350,
                    type: 'heatmap',
                },
                dataLabels: {
                    enabled: false
                },
                colors: ["#F3B415", "#F27036", "#663F59", "#6A6E94", "#4E88B4", "#00A7C6", "#18D8D8", '#A9D794','#46AF78'],
                series: heat_data,
                xaxis: {
                    type: 'category',
                },
                title: {
                    text: 'Horario de pedidos'
                },

            }

            var chart = new ApexCharts(
                document.querySelector("#apexchart-07"),
                options
            );

            chart.render();
        }*/
    });
</script>
@endsection