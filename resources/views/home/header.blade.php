<!-- header inner -->
         <div class="header">
            <div class="container">
               <div class="row">
                  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
                     <div class="full">
                        <div class="center-desk">
                           <div class="homelogo.jpg" style="padding-right: 70px;">
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
                                   <!-- Authenticated User Menu -->
                                   <li class="nav-item" style="padding-left: 20px; margin-left: auto;">
                                      <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                         <span class="nav-link" style="padding: 0; margin: 0; white-space: nowrap;">
                                            <strong>Hello, {{ Auth::user()->name }}</strong>
                                         </span>
                                         <a href="{{ route('profile.show') }}" class="btn btn-warning btn-sm" style="font-size: 0.875rem; padding: 5px 10px;">
                                            ⚙️ Profile Settings
                                         </a>
                                         <form method="POST" action="{{ route('logout') }}" style="margin: 0; display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm" style="font-size: 0.875rem; padding: 5px 10px;">
                                               🚪 Logout
                                            </button>
                                         </form>
                                      </div>
                                   </li>

                                   @else
                                   <!-- Guest User Menu -->
                                   <li class="nav-item" style="padding-left: 20px; margin-left: auto;">
                                      <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                         <a class="btn btn-success" href="{{ route('login') }}" style="font-size: 0.875rem;">Login</a>
                                         @if (Route::has('register'))
                                            <a class="btn btn-primary" href="{{ route('register') }}" style="font-size: 0.875rem;">Register</a>
                                         @endif
                                      </div>
                                   </li>
                                  @endauth
                              @endif
                           </ul>
                        </div>
                     </nav>
                  </div>
               </div>
            </div>
         </div>