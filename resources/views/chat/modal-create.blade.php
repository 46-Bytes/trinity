@php
    use App\Enums\Category;
    extract($viewVars);
@endphp
Choose a core category:

<form method="POST" action="{{ route('chat.createConversation') }}">
    @csrf
    <select class="form-select" id="category" name="category" required>
        @foreach($unusedCategories as $unusedCategory)
            <option value="{{ $unusedCategory->value }}">{{ $unusedCategory->label() }}</option>
        @endforeach
    </select>
    <br>
    <button type="submit" class="btn btn-primary">Create</button>
</form>
