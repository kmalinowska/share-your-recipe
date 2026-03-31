<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary w-full shadow-md hover:shadow-lg transition-all duration-300']) }}>
    {{ $slot }}
</button>
