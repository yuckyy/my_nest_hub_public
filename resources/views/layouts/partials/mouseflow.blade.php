@if (env('APP_ENV') == 'production')
<script type="text/javascript">
    window._mfq = window._mfq || [];
    (function() {
        var mf = document.createElement("script");
        mf.type = "text/javascript"; mf.defer = true;
        mf.src = "//cdn.mouseflow.com/projects/7c3d259b-12fe-4470-89dd-34d62946c641.js";
        document.getElementsByTagName("head")[0].appendChild(mf);
    })();
</script>
@endif