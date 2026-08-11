<!-- header inner -->
         <div class="header">
            <div class="container">
               <div class="row">
                  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
                     <div class="full">
                        <div class="center-desk">
                           <div class="logo" style="padding-right: 70px;">
                              <a href="{{url('/')}}"><img style="width: 300px; height: 100px; padding-bottom: 30px;" src="images/logoa.jpg" alt="homelogo" /></a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                     <nav class="navigation navbar navbar-expand-md navbar-dark ">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarsExample04">
                           <ul class="navbar-nav mr-auto" style="display: flex; flex-direction: row; align-items: center; flex-wrap: nowrap; white-space: nowrap;">
                              <li class="nav-item active" style="padding-right: 20px;">
                                 <a class="nav-link" href="{{url('/')}}">Home</a>
                              </li>
                              <li class="nav-item" style="padding-right: 20px;">
                                 <a class="nav-link" href="{{url('/#about')}}">About</a>
                              </li>
                              <li class="nav-item" style="padding-right: 20px;">
                                 <a class="nav-link" href="{{url('/#our_room')}}">Our room</a>
                              </li>
                              <li class="nav-item" style="padding-right: 20px;">
                                 <a class="nav-link" href="{{url('/#gallery')}}">Gallery</a>
                              </li>
                              <li class="nav-item" style="padding-right: 20px;">
                                 <a class="nav-link" href="{{url('/#contact')}}">Contact Us</a>
                              </li>
                           
                                @if (Route::has('login'))
                                   @auth

                                   <li class="nav-item d-flex align-items-center" style="padding-right: 20px;">
                                     <span class="nav-link" style="padding: 0;">Hello, {{ Auth::user()->name }}</span>
                                     <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="btn btn-link nav-link" style="padding-left:10px;">Logout</button>
                                     </form>
                                   </li>

                                   @else
                                      <li class="nav-item" style="padding-left: 20px;">
                                         <a class="btn btn-success" href="{{ route('login') }}">Login</a>
                                      </li>

                                      @if (Route::has('register'))
                                          <li class="nav-item" style="padding-left: 20px;">
                                             <a class="btn btn-primary" href="{{ route('register') }}">Register</a>
                                          </li>
                                      @endif
                                  @endauth
                              @endif
                           </ul>
                        </div>
                     </nav>
                  </div>
               </div>
            </div>
         </div>