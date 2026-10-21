<title>{{ env('APP_NAME') }}</title>
<meta charset="utf-8" />
<meta content="follow, index" name="robots" />
<link href="{{ url(request()->path()) }}" rel="canonical" />
<meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport" />
<meta content="" name="description" />
<meta content="{{ env('APP_URL') }}" name="twitter:site" />
<meta content="{{ env('APP_NAME') }}" name="twitter:creator" />
<meta content="summary_large_image" name="twitter:card" />
<meta content="{{ env('APP_NAME') }}" name="twitter:title" />
<meta content="" name="twitter:description" />
<meta content="{{ asset('assets/media/app/og-image.png') }}" name="twitter:image" />
<meta content="{{ url(request()->path()) }}" property="og:url" />
<meta content="en_US" property="og:locale" />
<meta content="website" property="og:type" />
<meta content="{{ env('APP_URL') }}" property="og:site_name" />
<meta content="{{ env('APP_NAME') }}" property="og:title" />
<meta content="" property="og:description" />
<meta content="{{ asset('assets/media/app/og-image.png') }}" property="og:image" />
@stack('meta')
<link href="{{ asset('assets/media/app/apple-touch-icon.png') }}" rel="apple-touch-icon" sizes="180x180" />
<link href="{{ asset('assets/media/app/favicon-32x32.png') }}" rel="icon" sizes="32x32" type="image/png" />
<link href="{{ asset('assets/media/app/favicon-16x16.png') }}" rel="icon" sizes="16x16" type="image/png" />
<link href="{{ asset('assets/media/app/favicon.ico') }}" rel="shortcut icon" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;1,100;1,200;1,300;1,400;1,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
<link href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet" />
