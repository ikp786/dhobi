 <!-- Jquery Core Js -->
 <script src="{{asset('assets/admin/bundles/libscripts.bundle.js')}}"></script>

 <!-- Plugin Js -->
 <script src="{{asset('assets/admin/bundles/apexcharts.bundle.js')}}"></script>
 <script src="{{asset('assets/admin/bundles/dataTables.bundle.js')}}"></script>

 <!-- Jquery Page Js -->
 <script src="{{asset('assets/admin/js/template.js')}}"></script>
 <script src="{{asset('assets/admin/js/page/index.js')}}"></script>
 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB1Jr7axGGkwvHRnNfoOzoVRFV3yOPHJEU&amp;callback=myMap"></script>
 <script src="{{asset('assets/admin/bundles/dropify.bundle.js')}}"></script>
 <script src="{{asset('assets/admin/plugin/cropper/cropper-init.js')}}"></script>
 <script src="{{asset('assets/admin/plugin/cropper/cropper.min.js')}}"></script>
 <script src="{{asset('assets/admin/plugin/cropper/cropper.min.js')}}"></script>
 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
 <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

 <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.4.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.4.1/js/buttons.print.min.js"></script>

<script src="{{asset('assets/admin/vendor/jquery-nice-select/js/jquery.nice-select.min.js')}}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.0/slimselect.min.js"></script>

 <script>
     $('#myDataTable')
         .addClass('nowrap')
         .dataTable({
             responsive: true,
             columnDefs: [{
                 targets: [-1, -3],
                 className: 'dt-body-right'
             }]
         });
 </script>