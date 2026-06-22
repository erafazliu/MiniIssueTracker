@if ($errors->any())
    <div class="validation-summary">
        <strong>Please fix the following:</strong>
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
