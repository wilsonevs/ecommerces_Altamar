<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Favicon -->
  <link rel="shortcut icon" href="{{plantilla.attrs.icono.data[0]|image}}" type="image/png" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" />
 <!-- ======== Swiper Js ======= -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/6.7.5/swiper-bundle.min.css"
    />

  <!-- Boxicons -->
  <link href='https://unpkg.com/boxicons@2.0.8/css/boxicons.min.css' rel='stylesheet'>
  <!-- Custom StyleSheet -->
  <link rel="stylesheet" href="{{template_url}}/altamar/src/public/css/foundation.css">
  <link rel="stylesheet" href="{{template_url}}/altamar/src/public/css/styles.css" />
  <link rel="stylesheet" href="{{template_url}}/altamar/src/public/css/modals.css" />
  <title>{{plantilla.attrs.titulo.data[0]}}</title>


  <script src="{{template_url}}/altamar/src/public/js/jquery.js"></script>
  <script src="{{template_url}}/altamar/src/public/js/what-input.js"></script>
  <script src="{{template_url}}/altamar/src/public/js/foundation.js"></script>

  <script>
  window.site_url = '{{site_url}}';
  window.rpcUrl = '{{site_url}}/public/server.php';
  {{ source('/altamar/src/public/js/predeterminado.js') }}
  </script>

</head>
<body class="{{getTemplateName()}}">