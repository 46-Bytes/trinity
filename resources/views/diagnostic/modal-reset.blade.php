This will reset the diagnostic data. Are you sure you want to continue?
<br><br>
<div class="d-flex justify-content-end">
    <form method="POST" action="{{ route('diagnostic.reset',$viewVars['conversation']->id) }}">
        @csrf
        <x-button type="submit" class="ms-3 btn btn-danger">Reset</x-button>
    </form>
</div>
