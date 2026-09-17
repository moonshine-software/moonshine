@props(['escapeHint' => moonshine()->getConfig()->isEscapeHint()])
<div {{ $attributes->class(['form-hint']) }}>{!! $escapeHint ? e($slot ?? '') : ($slot ?? '') !!}</div>
