<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary rounded-full text-sm normal-case tracking-normal']) }}>
    {{ $slot }}
</button>
