<!-- Navigation Menu -->
                <div class="list-group list-group-flush">
                    <a href="{{ route('member.dashboard') }}" 
                       class="list-group-item list-group-item-action active">
                       <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('member.profile') }}" 
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
                    <a href="{{ route('member.interests') }}" 
                       class="list-group-item list-group-item-action">
                       <i class="bi bi-tags me-2"></i>My Interests
                    </a>
                    <a href="{{ route('dashboard.events.index') }}" 
                       class="list-group-item list-group-item-action">
                       <i class="bi bi-calendar-event me-2"></i>Events
                    </a>
                    <a href="{{ route('dashboard.resources') }}" 
                       class="list-group-item list-group-item-action">
                       <i class="bi bi-file-earmark-text me-2"></i>Resources
                    </a>
                    <a href="{{ route('dashboard.payments') }}" 
                       class="list-group-item list-group-item-action">
                       <i class="bi bi-cash-stack me-2"></i>Payments
                    </a>
                    <a href="#" 
                       class="list-group-item list-group-item-action">
                       <i class="bi bi-people me-2"></i>Member Directory
                    </a>
                </div>