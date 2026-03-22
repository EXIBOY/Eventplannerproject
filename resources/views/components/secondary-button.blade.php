<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-secondary rounded-full text-sm normal-case tracking-normal disabled:cursor-not-allowed disabled:opacity-25']) }}>
    {{ $slot }}
</button>
