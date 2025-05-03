    @include('website.components.header')

<body>

     <!--===  Header Start ===-->
    {{-- @include('website.components.TopHeader') --}}
    @include('website.components.TopHeader', ['serviceCategory' => $serviceCategory])

    <!--===  Header End ===-->

    @yield('content')

    {{-- @include('frontend.components.footer') --}}


    <!--===  Footer Start ===-->

   @include('website.components.footer')

    <!--===  Footer End ===-->


</body>

</html>
