@extends('company.layouts.master')

@section('title', 'Applicant')
@section('page-title', 'Applicant Details')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('company.applicants.index') }}">Job Applicants</a></li>
  <li class="breadcrumb-item active">{{ $applicant->name ?? 'Applicant' }}</li>
@endsection

@php($teal = 'background:#0d6e6e;')

@push('page-styles')
<style>
  .applicant-page .card { border:0; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); overflow:hidden; }
  .applicant-page .card-header { border:0; padding:.85rem 1.1rem; }
  .applicant-hero { border-radius:14px; background:linear-gradient(135deg,#0d6e6e,#0a5757); }
  .applicant-hero .btn { border-radius:50rem; font-weight:600; }
  .applied-pill { background:#e7f4f4; color:#0d6e6e; border-radius:50rem; padding:.5rem 1rem; font-weight:600; }
  .info-list > div { padding:.45rem 0; border-bottom:1px solid #f1f5f9; font-size:.92rem; }
  .info-list > div:last-child { border-bottom:0; }
  .info-list .lbl { font-weight:700; color:#334155; display:inline-block; min-width:90px; }
  .applicant-photo { width:140px; height:140px; object-fit:cover; border-radius:14px; border:4px solid #e7f4f4; }
  .doc-btn { border-radius:50rem !important; font-weight:600; }

  /* Activity chat */
  .chat-card .card-header { display:flex; align-items:center; }
  .online-dot { width:9px; height:9px; border-radius:50%; background:#34d399; display:inline-block; margin-right:7px; box-shadow:0 0 0 3px rgba(52,211,153,.25); }
  #chat-messages { scrollbar-width:thin; }
  .chat-msg { margin-bottom:14px; }
  .msg-bubble { position:relative; max-width:88%; padding:8px 12px; border-radius:14px; border:1px solid #e8edf2; font-size:13px; box-shadow:0 1px 2px rgba(0,0,0,.04); }
  .msg-author { font-size:11px; font-weight:700; color:#0d6e6e; margin-bottom:2px; }
  .msg-time { font-size:10px; color:#94a3b8; margin-top:3px; }
  .msg-actions { position:absolute; top:-12px; right:8px; background:#fff; border:1px solid #e8edf2; border-radius:50rem; padding:1px 8px; box-shadow:0 2px 8px rgba(0,0,0,.1); opacity:0; transition:opacity .15s; white-space:nowrap; }
  .chat-msg:hover .msg-actions { opacity:1; }
  .chat-composer { display:flex; align-items:center; gap:4px; background:#fff; border:1px solid #e3e8ee; border-radius:50rem; padding:4px 6px; }
  .chat-composer .form-control { border:0; box-shadow:none !important; background:transparent; }
  .chat-composer .attach-btn { border:0; background:transparent; color:#64748b; border-radius:50%; }
  .chat-composer #chat-send { border-radius:50%; width:40px; height:40px; padding:0; background:#0d6e6e; border:0; flex:0 0 auto; }
</style>
@endpush

@section('content')
<div class="applicant-page">
  {{-- Header bar --}}
  <div class="card applicant-hero mb-3">
    <div class="card-body text-white d-flex flex-wrap justify-content-between align-items-center gap-2">
      <h4 class="mb-0">Applicant Details</h4>
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <a href="{{ $prev ? route('company.applicants.show', $prev) : '#' }}"
           class="btn btn-light btn-sm {{ $prev ? '' : 'disabled' }}"><i class="fa fa-arrow-left me-1"></i>Prev</a>
        <a href="{{ $next ? route('company.applicants.show', $next) : '#' }}"
           class="btn btn-light btn-sm {{ $next ? '' : 'disabled' }}">Next <i class="fa fa-arrow-right ms-1"></i></a>

        @if ($applicant->resume)
          <a href="{{ asset('resumes/'.$applicant->resume) }}" target="_blank" class="btn btn-danger btn-sm"><i class="fa fa-download me-1"></i>Download PDF</a>
        @endif

        @permission('applicants.edit')
          <form method="POST" action="{{ route('company.applicants.status', $applicant) }}" class="m-0">
            @csrf @method('PUT')
            <select name="status" id="detail-status" class="form-select form-select-sm"
                    style="min-width:170px; border-radius:50rem; padding-left:14px; background-color: {{ $statusColors[$applicant->status] ?? '#6c757d' }}; color:#fff; font-weight:600; border:none;"
                    onchange="recolorDetailStatus(this); this.form.submit()">
              @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" @selected($applicant->status === $key) style="background:#fff;color:#000;">{{ $label }}</option>
              @endforeach
            </select>
          </form>
        @endpermission

        <a href="{{ route('company.applicants.index') }}" class="btn btn-light btn-sm" title="Close"><i class="fa fa-times"></i></a>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- Main column --}}
    <div class="col-lg-9">
      <div class="mb-3">
        <span class="applied-pill">
          APPLIED FOR <strong>{{ $applicant->listing?->job_role ?? $applicant->position ?? '—' }}</strong>
        </span>
      </div>

      {{-- Personal Info --}}
      <div class="card mb-3">
        <div class="card-header text-white" style="{{ $teal }}"><h6 class="mb-0"><i class="fa fa-user me-2"></i>Personal Info</h6></div>
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-md-3 mb-3 text-center text-md-start">
              @if ($applicant->profile_image)
                <img src="{{ asset('profile_images/'.$applicant->profile_image) }}" alt="photo" class="applicant-photo">
              @else
                <div class="applicant-photo bg-info text-white d-inline-flex align-items-center justify-content-center" style="font-size:48px;font-weight:600;">
                  {{ strtoupper(substr($applicant->name ?? '?', 0, 1)) }}
                </div>
              @endif
            </div>
            <div class="col-md-9">
              <div class="info-list">
                <div><span class="lbl">Name:</span> {{ $applicant->name ?? '—' }}</div>
                <div><span class="lbl">Email:</span> {{ $applicant->email ?? '—' }}</div>
                <div><span class="lbl">Phone:</span> {{ $applicant->phone ?? '—' }}</div>
                <div><span class="lbl">Gender:</span> {{ $applicant->gender ?? '—' }}</div>
                <div><span class="lbl">Address:</span> {{ $applicant->address ?? '—' }}</div>
                <div><span class="lbl">Source:</span> {{ $applicant->source ?? '—' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Documents & Videos --}}
      <div class="card">
        <div class="card-header text-white" style="{{ $teal }}"><h6 class="mb-0"><i class="fa fa-folder me-2"></i>Documents &amp; Videos</h6></div>
        <div class="card-body d-flex flex-wrap gap-2">
          @if ($applicant->resume)
            <a href="{{ asset('resumes/'.$applicant->resume) }}" target="_blank" class="btn btn-outline-primary btn-sm doc-btn"><i class="fa fa-file me-1"></i>View Resume</a>
          @else
            <span class="btn btn-outline-danger btn-sm disabled doc-btn">Resume Not Uploaded</span>
          @endif

          @if ($applicant->intro_video)
            <a href="{{ asset('videos/'.$applicant->intro_video) }}" target="_blank" class="btn btn-outline-primary btn-sm doc-btn">Intro Video</a>
          @else
            <span class="btn btn-outline-danger btn-sm disabled doc-btn">Intro Video Not Uploaded</span>
          @endif

          @if ($applicant->screening_video)
            <a href="{{ asset('videos/'.$applicant->screening_video) }}" target="_blank" class="btn btn-outline-primary btn-sm doc-btn">Screening Video</a>
          @else
            <span class="btn btn-outline-danger btn-sm disabled doc-btn">Screening Video Not Uploaded</span>
          @endif

          @if ($applicant->portfolio)
            <a href="{{ asset('portfolio/'.$applicant->portfolio) }}" target="_blank" class="btn btn-outline-primary btn-sm doc-btn">Portfolio File</a>
          @else
            <span class="btn btn-outline-danger btn-sm disabled doc-btn">Portfolio File Not Uploaded</span>
          @endif

          @if ($applicant->portfolio_url)
            <a href="{{ $applicant->portfolio_url }}" target="_blank" class="btn btn-outline-primary btn-sm doc-btn">Portfolio URL</a>
          @else
            <span class="btn btn-outline-danger btn-sm disabled doc-btn">Portfolio URL Not Provided</span>
          @endif
        </div>
      </div>

      {{-- Education --}}
      <div class="card">
        <div class="card-header text-white" style="{{ $teal }}"><h6 class="mb-0"><i class="fa fa-graduation-cap me-2"></i>Education</h6></div>
        <div class="card-body">
          @if (! empty($applicant->education))
            <table class="table table-sm">
              <thead><tr><th>School</th><th>Program / Degree</th><th>Start</th><th>End</th></tr></thead>
              <tbody>
                @foreach ($applicant->education as $edu)
                  <tr>
                    <td>{{ $edu['school'] ?? '—' }}</td>
                    <td>{{ $edu['program'] ?? ($edu['degree'] ?? '—') }}</td>
                    <td>{{ $edu['startDate'] ?? '—' }}</td>
                    <td>{{ $edu['endDate'] ?? '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <p class="text-muted mb-0">No education details provided.</p>
          @endif
        </div>
      </div>

      {{-- Application answers --}}
      @if (! empty($applicant->answers))
        <div class="card">
          <div class="card-header text-white" style="{{ $teal }}"><h6 class="mb-0"><i class="fa fa-list me-2"></i>Answered Questions</h6></div>
          <div class="card-body">
            <table class="table table-sm">
              <tbody>
                @foreach ($applicant->answers as $key => $value)
                  <tr>
                    <th style="width:50%;">{{ is_string($key) ? $key : ($value['question'] ?? 'Q'.$loop->iteration) }}</th>
                    <td>{{ is_array($value) ? ($value['answer'] ?? json_encode($value)) : $value }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif
    </div>

    {{-- Activity chat panel --}}
    <div class="col-lg-3">
      <div class="card chat-card">
        <div class="card-header text-white" style="{{ $teal }}">
          <span class="online-dot"></span>
          <h6 class="mb-0"><i class="fa fa-comments me-2"></i>Activity Chat — {{ $applicant->name ?? 'Applicant' }}</h6>
        </div>
        <div class="card-body d-flex flex-column p-2" style="height:520px;background:#f7fafa;">
          <div id="chat-messages" class="flex-grow-1 overflow-auto mb-2 px-1"></div>

          <div id="file-preview" class="d-none align-items-center gap-2 mb-2 p-2 rounded" style="background:#f0f9f9;font-size:12px;">
            <span>📎</span><span id="file-preview-name" class="text-truncate" style="max-width:200px;"></span>
            <button type="button" class="btn-close ms-auto" id="file-remove" style="font-size:9px;"></button>
          </div>

          <div class="position-relative">
            <div id="mention-list" class="list-group position-absolute w-100 shadow"
                 style="bottom:52px; display:none; max-height:160px; overflow:auto; z-index:30; border-radius:12px; overflow:hidden;"></div>
            <div class="chat-composer">
              <label class="btn attach-btn mb-0" for="chat-file" title="Attach file"><i class="fa fa-paperclip"></i></label>
              <input type="file" id="chat-file" class="d-none" accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx">
              <input type="text" id="chat-input" class="form-control" placeholder="Type a message... use @ to mention" autocomplete="off">
              <button class="btn btn-primary text-white" type="button" id="chat-send"><i class="fa fa-paper-plane"></i></button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('page-scripts')
<script>
(function () {
  // Bind the chat exactly once, even if this script is evaluated twice
  // (theme re-runs / duplicate include) — otherwise every send fires twice.
  if (window.__applicantChatBound) return;
  window.__applicantChatBound = true;

  const CSRF = '{{ csrf_token() }}';
  const BASE = '{{ url('company/applicants/'.$applicant->id.'/comments') }}';
  const LIST_URL = '{{ route('company.applicants.comments.index', $applicant) }}';
  const USERS = @json($mentionables);
  const EMOJIS = ['👍','❤️','😂','😮','😢','🙏','🔥','👏'];

  const input = document.getElementById('chat-input');
  const sendBtn = document.getElementById('chat-send');
  const fileInput = document.getElementById('chat-file');
  const filePreview = document.getElementById('file-preview');
  const filePreviewName = document.getElementById('file-preview-name');
  const fileRemove = document.getElementById('file-remove');
  const mentionList = document.getElementById('mention-list');
  const messages = document.getElementById('chat-messages');
  const selected = new Map();
  let editing = false;
  let sending = false;

  function busy(on) {
    let bar = document.getElementById('chat-busy');
    if (on) {
      if (!bar) {
        bar = document.createElement('div');
        bar.id = 'chat-busy';
        bar.className = 'text-center text-muted py-1';
        bar.style.cssText = 'font-size:12px;';
        bar.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Processing…';
        messages.parentNode.insertBefore(bar, messages.nextSibling);
      }
    } else if (bar) {
      bar.remove();
    }
  }

  function esc(s){ const d=document.createElement('div'); d.textContent = s==null?'':s; return d.innerHTML; }

  function render(comments) {
    if (!comments.length) {
      messages.innerHTML = '<div class="d-flex flex-column justify-content-center align-items-center text-muted h-100"><i class="fa fa-comment-o fa-2x mb-2"></i><p class="mb-0">No messages yet.</p><small>Start the conversation!</small></div>';
      return;
    }
    messages.innerHTML = comments.map(renderOne).join('');
    messages.scrollTop = messages.scrollHeight;
  }

  function renderOne(c) {
    const side = c.mine ? 'justify-content-end' : '';
    if (c.deleted) {
      return `<div class="d-flex ${side} mb-2"><div class="px-2 py-1 rounded text-muted fst-italic" style="font-size:12px;background:#eef4f0;border:1px dashed #b8d4c4;">🚫 This message was deleted</div></div>`;
    }
    let media = '';
    if (c.attachment_type === 'image') media = `<a href="${c.attachment}" target="_blank"><img src="${c.attachment}" style="max-width:170px;border-radius:8px;margin-top:4px;display:block;"></a>`;
    else if (c.attachment_type === 'video') media = `<video src="${c.attachment}" controls style="max-width:190px;border-radius:8px;margin-top:4px;display:block;"></video>`;
    else if (c.attachment) media = `<a href="${c.attachment}" target="_blank" class="d-inline-block mt-1" style="font-size:12px;"><i class="fa fa-file me-1"></i>Attachment</a>`;

    const reactions = (c.reactions||[]).map(r =>
      `<span class="badge ${r.mine?'bg-primary':'bg-light text-dark border'} me-1" style="cursor:pointer;" data-react="${r.emoji}" data-id="${c.id}">${r.emoji} ${r.count}</span>`).join('');

    const actions = `<span style="font-size:13px;">
        <span class="me-2" style="cursor:pointer;" data-act="react" data-id="${c.id}" title="React">😊</span>
        ${c.editable ? `<span class="me-2" style="cursor:pointer;" data-act="edit" data-id="${c.id}" title="Edit (1 min)">✏️</span>` : ''}
        <span style="cursor:pointer;" data-act="del" data-id="${c.id}" data-mine="${c.mine?1:0}" title="Delete">🗑️</span>
      </span>`;

    const bg = c.mine ? '#dcf2ea' : '#ffffff';
    return `<div class="chat-msg" data-id="${c.id}">
        <div class="d-flex ${side}">
          <div class="msg-bubble" style="background:${bg};">
            ${!c.mine?`<div class="msg-author">${esc(c.author)}</div>`:''}
            ${c.body?`<div>${esc(c.body)}</div>`:''}
            ${media}
            <div class="msg-time">${c.time}${c.edited?' · edited':''}</div>
            <div class="msg-actions">${actions}</div>
          </div>
        </div>
        ${reactions?`<div class="d-flex ${c.mine?'justify-content-end':''} align-items-center gap-1 mt-1">${reactions}</div>`:''}
      </div>`;
  }

  function load() {
    fetch(LIST_URL, {headers:{'Accept':'application/json'}}).then(r=>r.json()).then(d=>{ if(!editing) render(d.comments||[]); });
  }

  function api(url, method, body) {
    const opt = { method, headers: { 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' } };
    if (body instanceof FormData) opt.body = body;
    else if (body) { opt.headers['Content-Type']='application/json'; opt.body = JSON.stringify(body); }
    return fetch(url, opt);
  }

  messages.addEventListener('click', function (e) {
    const react = e.target.closest('[data-react]');
    if (react) { toggleReact(react.dataset.id, react.dataset.react); return; }
    const act = e.target.closest('[data-act]');
    if (!act) return;
    const id = act.dataset.id;
    if (act.dataset.act === 'react') openEmojiPicker(act, id);
    else if (act.dataset.act === 'edit') startEdit(id);
    else if (act.dataset.act === 'del') openDeleteMenu(act, id, act.dataset.mine === '1');
  });

  function toggleReact(id, emoji){ api(BASE+'/'+id+'/react','POST',{emoji}).then(()=>load()); }

  function openEmojiPicker(anchor, id){
    closePopups();
    const p = document.createElement('div');
    p.className='emoji-pop bg-white shadow rounded p-1';
    p.style.cssText='position:absolute;z-index:1000;';
    p.innerHTML = EMOJIS.map(em=>`<span style="cursor:pointer;font-size:18px;padding:3px;" data-em="${em}">${em}</span>`).join('');
    document.body.appendChild(p);
    const r = anchor.getBoundingClientRect();
    p.style.left = r.left+'px'; p.style.top = (r.top-44+window.scrollY)+'px';
    p.addEventListener('click', ev=>{ const s=ev.target.closest('[data-em]'); if(s){ toggleReact(id, s.dataset.em); closePopups(); }});
  }

  function openDeleteMenu(anchor, id, mine){
    closePopups();
    const m = document.createElement('div');
    m.className='del-pop bg-white shadow rounded border';
    m.style.cssText='position:absolute;z-index:1000;min-width:160px;overflow:hidden;';
    m.innerHTML = `<button class="dropdown-item py-2" data-del="me">Delete for me</button>${mine?'<button class="dropdown-item py-2 text-danger" data-del="all">Delete for everyone</button>':''}`;
    document.body.appendChild(m);
    const r = anchor.getBoundingClientRect();
    m.style.left=Math.max(8,r.left-120)+'px'; m.style.top=(r.bottom+4+window.scrollY)+'px';
    m.addEventListener('click', ev=>{ const b=ev.target.closest('[data-del]'); if(!b) return;
      if(b.dataset.del==='me') api(BASE+'/'+id+'/delete-for-me','POST',{}).then(()=>load());
      else api(BASE+'/'+id,'DELETE',{}).then(()=>load());
      closePopups();
    });
  }

  function closePopups(){ document.querySelectorAll('.emoji-pop,.del-pop').forEach(e=>e.remove()); }
  document.addEventListener('click', e=>{ if(!e.target.closest('[data-act]') && !e.target.closest('.emoji-pop') && !e.target.closest('.del-pop')) closePopups(); });

  function startEdit(id){
    const msg = messages.querySelector('.chat-msg[data-id="'+id+'"] .msg-bubble > div:not([class])');
    const current = msg ? msg.textContent : '';
    const text = prompt('Edit message (allowed within 1 minute):', current);
    if (text === null || !text.trim()) return;
    busy(true);
    api(BASE+'/'+id,'PUT',{body:text}).then(r=>{ if(!r.ok) r.json().then(d=>alert(d.message||'Cannot edit (1-minute window passed).')); load(); }).finally(()=>busy(false));
  }

  function showMentions(query){
    const q=query.toLowerCase();
    const matches=USERS.filter(u=>u.name.toLowerCase().includes(q)).slice(0,8);
    if(!matches.length){ mentionList.style.display='none'; return; }
    mentionList.innerHTML = matches.map(u=>`<button type="button" class="list-group-item list-group-item-action py-1" data-id="${u.id}" data-name="${esc(u.name)}"><i class="fa fa-user me-2"></i>${esc(u.name)}</button>`).join('');
    mentionList.style.display='block';
  }
  input.addEventListener('input', ()=>{ const m=input.value.match(/@([\w .\-]*)$/); m?showMentions(m[1]):(mentionList.style.display='none'); });
  mentionList.addEventListener('click', e=>{ const b=e.target.closest('button'); if(!b) return; selected.set(b.dataset.name.toLowerCase(), b.dataset.id); input.value=input.value.replace(/@([\w .\-]*)$/,'@'+b.dataset.name+' '); mentionList.style.display='none'; input.focus(); });

  fileInput.addEventListener('change', ()=>{ if(fileInput.files.length){ filePreviewName.textContent=fileInput.files[0].name; filePreview.classList.remove('d-none'); filePreview.classList.add('d-flex'); }});
  fileRemove.addEventListener('click', ()=>{ fileInput.value=''; filePreview.classList.add('d-none'); filePreview.classList.remove('d-flex'); });

  function send(){
    if (sending) return;                       // guard against double-submit
    const body=input.value.trim();
    if(!body && !fileInput.files.length) return;
    const fd=new FormData();
    fd.append('body', body);
    selected.forEach((id,name)=>{ if(body.toLowerCase().includes('@'+name)) fd.append('mentions[]', id); });
    if(fileInput.files.length) fd.append('attachment', fileInput.files[0]);
    sending=true; sendBtn.disabled=true; busy(true);
    api(BASE,'POST',fd).then(r=>r.json()).then(()=>{ input.value=''; selected.clear(); fileRemove.click(); load(); })
      .finally(()=>{ sending=false; sendBtn.disabled=false; busy(false); });
  }
  sendBtn.addEventListener('click', send);
  input.addEventListener('keydown', e=>{ if(e.key==='Enter' && !e.repeat && mentionList.style.display==='none'){ e.preventDefault(); send(); }});

  window.STATUS_COLORS = @json($statusColors);
  window.recolorDetailStatus = function(sel){ const c=STATUS_COLORS[sel.value]||'#6c757d'; sel.style.setProperty('background-color',c,'important'); sel.style.setProperty('color','#fff','important'); };

  load();
  setInterval(load, 20000);
})();
</script>
@endpush
