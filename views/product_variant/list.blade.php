@extends('layouts.admin.layer')

@section('content')
<div class="content__pg--variant-list">
   {{-- 产品规格列表 --}}
   <h3 class="variant-list-header">请选择要编辑的型号</h3>
   <ul class="variant-list">
    @foreach ($variants as $variant)
        <li data-id="{{$variant->getId()}}" class="variant">{{$variant->getModel()}}</li>
    @endforeach
   </ul>
</div>
@endsection

@push('body-scripts')
    <script>
        $('.variant').on('click', function (e) {
            let variant_id = $(this).data('id');
            location = '/admin/shop_product_variant/edit/?variant_id='+variant_id;
        });
    </script>
@endpush
