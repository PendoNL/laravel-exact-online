<form method="post" action="{{ route('exact.authorize') }}">
    {{ csrf_field() }}
    <button class="button small orange" type="submit">Verbinden met Nationale Interim Bank Exact App</button>
</form>