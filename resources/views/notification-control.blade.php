 <style>
     .dibaca p {
         color: #969696 !important;
         font-weight: 300;
     }

     .dibaca h5 {
         font-weight: 300;
     }

     .dibaca svg {
         color: #aeaeae !important;
     }
 </style>
 <li class="dropdown pc-h-item">
     <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button"
         aria-haspopup="false" aria-expanded="false">
         <svg class="pc-icon">
             <use xlink:href="#custom-notification"></use>
         </svg>
         @if ($notifikasi_counter > 0)
             <span class="badge bg-success pc-h-badge">{{ $notifikasi_counter }}</span>
         @endif
     </a>
     <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
         <div class="dropdown-header d-flex align-items-center justify-content-between">
             <h5 class="m-0">Notifikasi</h5>
             @if (count($notifikasi))
                 <a href="#!" class="btn btn-link btn-sm mark-all-read">Tandai Semua Dibaca</a>
             @endif
         </div>
         <div class="dropdown-body text-wrap header-notification-scroll position-relative"
             style="max-height: calc(100vh - 215px)">
             @if (count($notifikasi))
                 @foreach ($notifikasi as $item)
                     <a href="{{ route('notifikasi.read', ['id' => $item->id]) }}"
                         class="card mb-2 @if ($item->dibaca) dibaca @endif">
                         <div class="card-body">
                             <div class="d-flex">
                                 <div class="flex-shrink-0">
                                     <svg class="pc-icon text-primary">
                                         <use xlink:href="#custom-sms"></use>
                                     </svg>
                                 </div>
                                 <div class="flex-grow-1 ms-3">
                                     <span class="float-end text-sm text-muted">{{ $item->time_ago }}</span>
                                     <h5 class="text-body mb-2 text-truncate">{{ $item->title }}</h5>
                                     <p class="mb-0" title="{{ $item->pesan }}">
                                         {!! $item->tag !!}</p>
                                 </div>
                             </div>
                         </div>
                     </a>
                 @endforeach
             @else
                 <div class="alert alert-info">Tidak ada notifikasi</div>
             @endif
         </div>
         @if (count($notifikasi))
             <div class="text-center py-2 clear-notification">
                 <a href="#!" class="link-danger">Bersihkan notifikasi</a>
             </div>
         @endif
     </div>
 </li>
