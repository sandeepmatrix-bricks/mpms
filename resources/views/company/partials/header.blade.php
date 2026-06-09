<div class="page-header">
  <div class="header-wrapper row m-0">
    <div class="header-logo-wrapper col-auto p-0">
      <div class="logo-wrapper">
        <a href="{{ route('company.dashboard') }}">
          <img class="img-fluid for-light" src="{{ asset('admin-assets/images/logo/logo_dark.png') }}" alt="logo">
          <img class="img-fluid for-dark" src="{{ asset('admin-assets/images/logo/logo.png') }}" alt="logo">
        </a>
      </div>
      <div class="toggle-sidebar">
        <i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
      </div>
    </div>

    <div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
      <div class="d-flex align-items-center gap-2">
        <h4 class="f-w-600 mb-0">{{ $company->name }}</h4>
        <span class="badge badge-light-info">Company</span>
      </div>
    </div>

    <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
      @php($myMentions = \App\Models\Mention::with(['comment', 'mentionedBy', 'applicant'])
            ->where('mentioned_user_id', auth()->id())
            ->latest()->limit(10)->get())
      @php($unreadMentions = $myMentions->whereNull('read_at')->count())
      <ul class="nav-menus">
        <li class="onhover-dropdown p-0">
          <div class="notification-box position-relative" style="cursor:pointer;">
            <i data-feather="bell"></i>
            @if ($unreadMentions > 0)
              <span class="badge rounded-pill badge-danger position-absolute top-0 start-100 translate-middle">{{ $unreadMentions }}</span>
            @endif
          </div>
          <div class="onhover-show-div notification-dropdown p-0" style="min-width:320px;">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
              <h6 class="mb-0"><i class="fa fa-bell me-1 text-warning"></i> Mentions ({{ $unreadMentions }})</h6>
              @if ($unreadMentions > 0)
                <form method="POST" action="{{ route('company.mentions.read') }}" class="m-0">
                  @csrf
                  <button type="submit" class="btn btn-link btn-sm text-success p-0">✓ All read</button>
                </form>
              @endif
            </div>
            <ul class="list-unstyled mb-0" style="max-height:320px; overflow:auto;">
              @forelse ($myMentions as $mention)
                <li class="border-bottom">
                  <a href="{{ $mention->applicant ? route('company.applicants.show', $mention->applicant) : '#' }}"
                     class="d-block p-3 text-decoration-none {{ $mention->read_at ? '' : 'bg-light' }}">
                    <div class="d-flex justify-content-between">
                      <strong class="text-dark">{{ $mention->mentionedBy?->name ?? 'Someone' }}</strong>
                      <span class="badge badge-light-{{ $mention->read_at ? 'success' : 'warning' }}">{{ $mention->read_at ? '✓ Read' : 'New' }}</span>
                    </div>
                    <div class="text-muted small">mentioned you: "{{ \Illuminate\Support\Str::limit($mention->comment?->body, 40) }}"</div>
                    <div class="text-muted" style="font-size:11px;">{{ $mention->created_at?->diffForHumans() }}</div>
                  </a>
                </li>
              @empty
                <li class="p-3 text-center text-muted">No mentions yet.</li>
              @endforelse
            </ul>
          </div>
        </li>

        <li class="profile-nav onhover-dropdown p-0">
          <div class="d-flex profile-media align-items-center">
            <div class="rounded-circle bg-info d-flex align-items-center justify-content-center text-white"
                 style="width:36px;height:36px;font-weight:600;">
              {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-grow-1 ms-2">
              <span>{{ auth()->user()->name }}</span>
              <p class="mb-0 font-roboto">Company <i class="middle fa fa-angle-down"></i></p>
            </div>
          </div>
          <ul class="profile-dropdown onhover-show-div">
            <li><span class="text-muted small px-2">{{ auth()->user()->email }}</span></li>
            <li>
              <form method="POST" action="{{ route('company.logout') }}">
                @csrf
                <button type="submit" class="dropdown-item border-0 bg-transparent p-0 d-flex align-items-center gap-2">
                  <i data-feather="log-out"></i><span>Log out</span>
                </button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
