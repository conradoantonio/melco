@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class="bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <h1>Categorías</h1>
                </div>
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
                            <li class="breadcrumb-item active" aria-current="page"><a
                                    href="{{url('categorias/')}}"></a>Formulario</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container pull-up">
        <div class="row" style="background-color: #fff;">
            {{-- Table --}}
            <div class="col-lg-7 m-b-30">
                <div class="card">
                    <div class="card-header">
                        <h2 class="">Ingresa la descripción de la categoría</h2>
                    </div>
                    <div class="card-body">
                        <form id="form-data" action="{{url('categorias/'.($item->exists ? 'update' : 'save'))}}"
                            onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off"
                            data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1"
                            data-table_id="example3" data-container_id="table-container">
                            <div class="form-group floating-label" style="display: none;">
                                <label>ID</label>
                                <input type="text" class="form-control" name="id" value="{{$item->exists ? $item->id : ''}}">
                            </div>
                            <div class="form-group floating-label">
                                <label>Descripción</label>
                                <input type="text" class="form-control not-empty" name="nombre"
                                    value="{{$item->exists ? $item->nombre : ''}}" placeholder="Descripción de la categoría"
                                    data-msg="Descripción">
                            </div>
                            <a href="{{url('categorias')}}"><button type="button"
                                    class="btn btn-primary">Regresar</button></a>
                            <button type="submit" class="btn btn-success save">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-5">
                <div class="image-featured-box" @if($item->exists) output onclick="changeImg()"
                  @endif >
                  <img width="auto" height="100%" id="preview_image"
                    src="{{$item->image ? asset($item->image) : asset('/uploads/noimage.png')}}" />
                </div>
                <input type="file" id="file" style="display: none" />
                <input type="hidden" id="file_name" />
              </div>
        </div>
    </div>
</section>

<script>
    function changeImg() {
          $('#file').click();
      }
      $('#file').change(function () {
          if ($(this).val() != '') {
              upload(this);

          }
      });
      function upload(img) {
          var form_data = new FormData();
          form_data.append('file', img.files[0]);
          form_data.append('_token', '{{csrf_token()}}');
          $('#loading').css('display', 'block');
          $.ajax({
              url: "{{url('/categorias/' . $item->id . '/image-upload')}}",
              data: form_data,
              type: 'POST',
              contentType: false,
              processData: false,
              success: function (data) {
                  if (data.fail) {
                      $('#preview_image').attr('src', '{{asset('images/noimage.jpg')}}');
                      alert(data.errors['file']);
                  }
                  else {
                      $('#file_name').val(data);
                      $('#preview_image').attr('src', data['filename']);
                  }
                  $('#loading').css('display', 'none');
              },
              error: function (xhr, status, error) {
                  alert(xhr.responseText);
                  $('#preview_image').attr('src', '{{asset('images/noimage.jpg')}}');
              }
          });
      }
  </script>

@endsection
