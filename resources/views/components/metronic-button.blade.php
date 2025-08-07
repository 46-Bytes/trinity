@props([ 'buttonText'=> 'button', 'link'=>'#', 'class' => 'btn btn-primary', 'style'=>''])

<a href="{{ $link }}" style="{{ $style }}" class="{{ $class }} fw-bold w-100 mb-8">{{ $buttonText }}</a>
