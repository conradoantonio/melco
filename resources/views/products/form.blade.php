@extends('layouts.main')
<script src="https://unpkg.com/vue-multiselect@2.1.0"></script>

@section('content')
<section class="admin-content">
  <div class="bg-dark m-b-30 bg-stars">
    <div class="container">
      <div class="row">
        <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
          <h1>Productos</h1>
        </div>
        <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
              <li class="breadcrumb-item active" aria-current="page"><a href="{{url('products/')}}"></a>Formulario</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <div class="container pull-up">
    <div class="row">
      {{-- Table --}}
      <div class="col-lg-12 m-b-30">
        <div class="card">
          <div class="card-header">
            <h2 class="">Ingresa la descripción del producto</h2>
          </div>
          <div id="app" class="card-body">
            <form id="form-data" action="{{url('products/'.($item ? $item->id  : ''))}}" onsubmit="return false;"
              enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0"
              data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
              @if($item->exists)
              <input type="hidden" name="_method" value="PUT">
              @endif
              <div class="form-group floating-label" style="display: none;">
                <label>ID</label>
                <input type="text" class="form-control" name="id" value="{{$item ? $item->id : ''}}">
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <div class="form-row">
                    <label>Presentación</label>
                    <input type="text" class="form-control not-empty" name="name" value="{{$item ? $item->name : ''}}"
                      placeholder="Descripción del producto" data-msg="Presentación">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <label for="category_id">Categoría</label>
                      <select name="category_id" class="form-control not-empty select2" data-msg="Categoría">
                        <option value="0" selected disabled>Seleccione una opción</option>
                        @if( $item )
                        @foreach($categorias as $categoria)
                        <option value="{{$categoria->id}}" {{($item->category_id == $categoria->id ? 'selected' : '')}}>
                          {{$categoria->nombre}}</option>
                        @endforeach
                        @else
                        @foreach($categorias as $categoria)
                        <option value="{{$categoria->id}}">{{$categoria->nombre}}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <label>Descripción</label>
                      <textarea type="text" class="form-control not-empty" rows="15" name="description" maxlength="1000" data-msg="Descripción" placeholder="Escriba un máximo de 1000 caracteres...">{{ $item->description}}</textarea>

                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-12">
                      <label for="tags" class="control-label">Tamaños</label>

                      <tag-multiselect id="typeSizes" :type="2" :product="{{ $item-> id }}" v-model="vsizes"
                        @update="usizes">

                      </tag-multiselect>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-12">
                      <label for="tags" class="control-label">Calidades</label>
                      <tag-multiselect id="typeQualities" :type="1" :product="{{ $item-> id }}" @update="uqualities">
                      </tag-multiselect>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <label for="tags" class="control-label">Precios</label>
                      <variant-price v-if="(vqualities.length && vsizes.length)" :headers-h="vsizes" :headers-v="vqualities"
                    :product="{{ $item->id }}" :variants='@json($product_variants)'></variant-price>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-4">
                      <label>Precio compra</label>
                      <input type="number" class="form-control not-empty" name="price_cost"
                        value="{{$item ? $item->price_cost : ''}}" placeholder="" data-msg="Precio compra">
                    </div>
                    <div class="form-group col-md-4">
                      <label>Precio por kilo</label>
                      <input type="number" class="form-control not-empty" name="price_weight"
                        value="{{ $item ? $item->price_weight : ''}}" placeholder="" data-msg="Precio por kilo">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>Estado</label>
                      <input type="text" class="form-control not-empty" name="location"
                        value="{{$item ? $item->location : ''}}" placeholder="" data-msg="Estado">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Stock</label>
                      <input type="text" class="form-control not-empty" name="stock"
                        value="{{$item ? $item->stock : ''}}" placeholder="" data-msg="Stock">
                    </div>
                  </div>
                </div>
                <div class="form-group col-md-6">
                  <div class="image-featured-box" @if($item->exists) output onclick="changeImg()"
                    @endif >
                    <img width="auto" height="100%" id="preview_image"
                      src="{{$item->featured_image ? asset($item->featured_image) : asset('/uploads/noimage.png')}}" />
                  </div>
                  <input type="file" id="file" style="display: none" />
                  <input type="hidden" id="file_name" />
                  <br><br>
                  <!--
                  <label>Destacar</label>
                  <input type="checkbox" class="" name="featured" value="{{$item ? $item->featured : ''}}"
                    placeholder="Descripción del producto" data-msg="Descripción">
                    -->
                </div>
              </div>

              <a href="{{url('products')}}"><button type="button" class="btn btn-primary">Regresar</button></a>
              <button type="submit" class="btn btn-success save">Guardar</button>
            </form>
          </div>
          <script src="{{asset('js/app.js')}}"></script>

        </div>
      </div>
    </div>
  </div>
</section>

<script>
  $(document).ready(function () {


  });

  $('#pricesModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('whatever') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    var modal = $(this)
    modal.find('.modal-title').text('New message to ' + recipient)
    modal.find('.modal-body input').val(recipient)
  })

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
            url: "{{url('/products/' . $item->id . '/image-upload')}}",
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
