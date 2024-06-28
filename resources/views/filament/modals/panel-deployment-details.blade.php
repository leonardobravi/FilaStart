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

    <pre id="deployment-log" class="text-white p-4"
         style="background-color: #1f2937; max-height: 50vh; overflow: auto;"
         @if($panelDeployment->status !== 'success')
             wire:poll.1000ms
         @endif
    >{!! ($panelDeployment->deployment_log) !!}</pre>

    {{-- TODO Find a performant solution --}}
    {{-- @if($panelDeployment->status !== 'success')
         @script
         <script>
             setInterval(() => {
                 let element = document.getElementById('deployment-log');
                 if (element) {
                     element.scrollTop = element.scrollHeight;
                 }
             }, 1050);
         </script>
         @endscript
     @endif--}}
</div>
