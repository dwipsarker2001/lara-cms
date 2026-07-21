@php $d = $data; @endphp
@if($d['dividerImage'] ?? false)
    <section data-block="divider" style="line-height: 0; position: relative; z-index: 10;">
        <div style="position: relative; width: 100%; max-width: 1240px; margin: 0 auto;">
            <img data-edit="dividerImage" decoding="auto" width="1240" sizes="(min-width: 1200px) max(1200px, min(100vw - 80px, 1240px), min(100vw - 80px, 1030px)), (min-width: 810px) and (max-width: 1199.98px) max(1200px, min(100vw - 60px, 900px)), (max-width: 809.98px) max(1200px, min(100vw - 80px, 1240px), min(100vw - 32px, 450px))" srcset="{{ $d['dividerImage'] }}?scale-down-to=512&width=1240&height=10 512w, {{ $d['dividerImage'] }}?scale-down-to=1024&width=1240&height=10 1024w, {{ $d['dividerImage'] }}?width=1240&height=10 1240w" src="{{ $d['dividerImage'] }}?width=1240&height=10" alt="" style="position: absolute; top: -5px; left: 0; width: 100%; height: auto; -webkit-mask-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,1) 15%, rgba(0,0,0,1) 85%, rgba(0,0,0,0) 100%); mask-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,1) 15%, rgba(0,0,0,1) 85%, rgba(0,0,0,0) 100%);" />
        </div>
    </section>
@endif
