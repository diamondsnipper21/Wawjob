        
        <div class="{{ $_inn_blade_row_class }}">
        @for ($i = 0; $i < count($_inn_blade_content); $i++)
            <div class="{{ $_inn_blade_class[$i] or '' }}">
              {!! $_inn_blade_content[$i] or '' !!}
            </div>
        @endfor
        </div>