@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'input input-bordered w-full bg-white text-gray-900 focus:outline-primary border-gray-300']) }}>
