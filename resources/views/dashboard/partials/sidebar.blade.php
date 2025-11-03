<!-- Navigation Menu -->
                <div class="list-group list-group-flush">
                    <a href="{{ route('member.dashboard') }}" 
                       class="list-group-item list-group-item-action active">
                       <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a href="#" 
                       class="list-group-item list-group-item-action">
                       <i class="bi bi-person me-2"></i>My Profile
                    </a>
                    {{-- <a href="{{ route('dashboard.certificates') }}" 
                       class="list-group-item list-group-item-action">
                       <i class="bi bi-award me-2"></i>Certificates
                    </a> --}}
                    <a href="{{ route('dashboard.request-certificate') }}"
                        class="list-group-item list-group-item-action">
                        <i class="bi bi-award me-2"></i> Certificates
                     </a>
                    <a href="#" 
                       class="list-group-item list-group-item-action">
                       <i class="bi bi-tags me-2"></i>My Interests
                    </a>
                    <a href="#" 
                       class="list-group-item list-group-item-action">
                       <i class="bi bi-calendar-event me-2"></i>Events
                    </a>
                    <a href="#" 
                       class="list-group-item list-group-item-action">
                       <i class="bi bi-file-earmark-text me-2"></i>Resources
                    </a>
                    <a href="#" 
                       class="list-group-item list-group-item-action">
                       <i class="bi bi-people me-2"></i>Member Directory
                    </a>
                </div>