@if (count($errors) > 0)
    <!-- Form Error List -->
    <div class="alert alert-danger">
        <strong>Whoops! Something went wrong!</strong>

        <br><br>
        @if (is_array($errors))
            
            <ul>
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        @else

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        @endif
        

    </div>
@endif