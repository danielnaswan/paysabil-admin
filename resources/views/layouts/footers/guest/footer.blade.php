  <!-- -------- START FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
  <footer class="footer py-5">
    <div class="container">
      <div class="row">
      <div class="col-lg-8 mb-4 mx-auto text-center">
          <a href="https://www.uthm.edu.my/en/" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
              Company
          </a>
          <a href="https://www.linkedin.com/in/danielnaswan/" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
              About Me
          </a>
          {{-- <a href="https://www.creative-tim.com/presentation" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
              Team
          </a> --}}
          {{-- <a href="https://www.creative-tim.com/templates" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
              Products
          </a> --}}
          {{-- <a href="https://www.creative-tim.com/blog" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
              Blog
          </a> --}}
          {{-- <a href="https://www.creative-tim.com/support-terms" target="_blank" class="text-secondary me-xl-5 me-3 mb-sm-0 mb-2">
              Pricing
          </a> --}}
      </div>
        @if (!auth()->user() || \Request::is('static-sign-up')) 
          <div class="col-lg-8 mx-auto text-center mb-4 mt-2">
              <a href="https://dribbble.com/creativetim" target="_blank" class="text-secondary me-xl-4 me-4">
                  <span class="text-lg fab fa-dribbble" aria-hidden="true"></span>
              </a>
              <a href="https://twitter.com/CreativeTim" target="_blank" class="text-secondary me-xl-4 me-4">
                  <span class="text-lg fab fa-twitter" aria-hidden="true"></span>
              </a>
              <a href="https://www.instagram.com/creativetimofficial/" target="_blank" class="text-secondary me-xl-4 me-4">
                  <span class="text-lg fab fa-instagram" aria-hidden="true"></span>
              </a>
              <a href="https://ro.pinterest.com/thecreativetim/" target="_blank" class="text-secondary me-xl-4 me-4">
                  <span class="text-lg fab fa-pinterest" aria-hidden="true"></span>
              </a>
              <a href="https://github.com/creativetimofficial" target="_blank" class="text-secondary me-xl-4 me-4">
                  <span class="text-lg fab fa-github" aria-hidden="true"></span>
              </a>
          </div>
        @endif
      </div>
      @if (!auth()->user() || \Request::is('static-sign-up')) 
        <div class="row">
          <div class="col-8 mx-auto text-center mt-1">
            <p class="mb-0 text-secondary">
              Copyright © <script>
                document.write(new Date().getFullYear())
              </script> by 
              <a style="color: #252f40;" href="https://www.linkedin.com/in/danielnaswan/" class="font-weight-bold ml-1" target="_blank">danielnaswan</a>
              &
              <a style="color: #252f40;" href="https://hep.uthm.edu.my/ms/" class="font-weight-bold ml-1" target="_blank">HEPAUTHM</a>.
            </p>
          </div>
        </div>
      @endif
    </div>
  </footer>
  <!-- -------- END FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
