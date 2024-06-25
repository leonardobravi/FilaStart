<div class="">
    @if($panelDeployment->status == 'success')
        <div class="text-success">

            <div class="p-4">
                You can download this release files from here:

                <x-filament::link target="_blank" href="{{ $panelDeployment->file_path }}">
                     Zip Archive
                </x-filament::link>
            </div>
        </div>
    @endif
    {{--<div class="text-white p-4"
         style="background-color: #1f2937; max-height: 50vh; overflow: auto;"
         @if($panelDeployment->status !== 'success')
             wire:poll.1000ms
         @endif
         id="deployment-log"
    >
        {!! nl2br($panelDeployment->deployment_log) !!}
    </div>--}}
    <pre class="text-white p-4"
         style="background-color: #1f2937; max-height: 50vh; overflow: auto;"
         @if($panelDeployment->status !== 'success')
             wire:poll.1000ms
         @endif
         id="deployment-log"
    >{!! ($panelDeployment->deployment_log) !!}</pre>

    @script
    <script>
        let element = document.getElementById('deployment-log');
        element.scrollTop = element.scrollHeight;
    </script>
    @endscript
</div>
