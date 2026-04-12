@props(['status'])

@php
    $badgeClass = '';
    switch ($status) {
        case 'Lunas':
            $badgeClass = 'bg-label-success';
            break;
        case 'Belum Lunas':
            $badgeClass = 'bg-label-warning';
            break;
        case 'Dibatalkan':
            $badgeClass = 'bg-label-danger';
            break;
        case 'Lunas Sebagian':
            $badgeClass = 'bg-label-info';
            break;
        default:
            $badgeClass = 'bg-label-secondary';
            break;
    }
@endphp

<span class="badge bg-label-sm {{ $badgeClass }}">{{ $status }}</span>
