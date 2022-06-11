<div class="antialiased sans-serif bg-gray-100 h-screen">
	<div x-data="app()" x-init="[initDate(), getEvents(),]" x-cloak>
		<div class="container mx-auto px-4 py-4">
			  
			<!-- <div class="font-bold text-gray-800 text-xl mb-4">
				Schedule Tasks
			</div> -->

			<div class="bg-white rounded-lg shadow overflow-hidden">

				<div class="flex items-center justify-between py-2 px-6">
					<div>
						<span class="text-lg font-bold text-gray-800">{{$displayMonth}}</span>
						<span class="ml-1 text-lg text-gray-600 font-normal">{{$year}}</span>
					
					</div>
					<div class="border rounded-lg px-1" style="padding-top: 2px;">
						<button 
							type="button"
							class="leading-none rounded-lg transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-200 p-1 items-center" wire:click="prevMonth('{{$selectedMonth}}')">
							<svg class="h-6 w-6 text-gray-500 inline-flex leading-none"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
							</svg>  
						</button>
						<div class="border-r inline-flex h-6"></div>		
						<button 
							type="button"
							class="leading-none rounded-lg transition ease-in-out duration-100 inline-flex items-center cursor-pointer hover:bg-gray-200 p-1" 
							wire:click="nextMonth('{{$selectedMonth}}')">
							<svg class="h-6 w-6 text-gray-500 inline-flex leading-none"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
							</svg>									  
						</button>
					</div>
				</div>	
				<div class="flex flex-wrap" >
					@foreach ($days as $day)
					<div style="width: 14.26%" class="px-2 py-2 bg-indigo-600 ">
						<div class="text-white text-sm uppercase tracking-wide font-bold text-center">{{$day}}</div>
					</div>
					@endforeach
				</div>
				<div class="-mx-1 -mb-1">

					<div class="flex flex-wrap border-t border-l" x-data="{ adding:false, removing:false }">
						@if ($previousDays)
						@foreach ($previousDays as $prevDays)
							<div 
								style="width: 14.28%; height: 120px"
								class="border-r border-b px-4 pt-2 bg-gray-50"	
							>{{$prevDays}}</div>
							@endforeach
							@endif

							{{--  Begin current month  --}}
							@foreach ($no_of_days as $key => $date)
							
							<div  style="width: 14.28%; height: 120px" class="px-4 pt-2 border-r border-b relative" >
								<div x-on:click="$wire.newEvent('{{$date}}')"
									class="inline-flex w-7 h-7 items-center justify-center cursor-pointer text-center leading-none rounded-full transition ease-in-out duration-100"
									:class="{'text-white bg-indigo-600': '{{$date}}' === '{{$dayNumber}}'}"	>{{$date}}
								</div>
									
										<div id="'{{$date}}'" style="height: 80px;" class="overflow-y-auto mt-1" 
										x-on:drop.prevent="const id = event.dataTransfer.getData('text/plain');
										const target = event.target.closest('div');
										const element = document.getElementById(id);
										target.appendChild(element);console.log(target.id)
										@this.reorderEvent(id,target.id);" x-on:dragover.prevent="adding= true"  x-on:dragleave.prevent="adding=false">
											@if ($events)
											@foreach ($events as $key => $event)
											@if ($date == date('d',strtotime($event->start_date)))
											
													
										<input id="event{{$event->id}}" draggable="true" x-data="{ dragging: false }"
										x-on:dragstart.self="dragging = true;
										event.dataTransfer.effectAllowed = 'move';
  										event.dataTransfer.setData('text/plain', event.target.id);
										console.log(event.target.id)"
										x-on:dragend="dragging = false"
										wire:key="event-{{ $event->id }}" 
										x-on:dblclick="$wire.editEvent('{{$event->id}}')"
										x-on:keyup.delete="$wire.deleteEvent('{{$event->id}}')"
										wire:keydown.enter="changeTitleEvent({{ $event->id }}, $event.target.value)"
											class="px-2 py-1 rounded-lg mt-1 overflow-hidden border cursor-move w-full"
											:class="{
												'border-blue-200 text-blue-800 bg-blue-100': '{{$event->theme}}' === 'blue',
												'border-red-200 text-red-800 bg-red-100': '{{$event->theme}}' === 'red',
												'border-yellow-200 text-yellow-800 bg-yellow-100': '{{$event->theme}}' === 'yellow',
												'border-green-200 text-green-800 bg-green-100': '{{$event->theme}}' === 'green',
												'border-purple-200 text-purple-800 bg-purple-100': '{{$event->theme}}'=== 'purple'
											}" value="{{ $event->title}}" />
										@endif
										@endforeach
										@endif
								</div>
								
							</div>
							@endforeach
							
							@if ($nextDays)
							@foreach ($nextDays as $nextDays)
								<div 
									style="width: 14.28%; height: 120px"
									class=" border-r border-b px-4 pt-2 bg-gray-50"	
								>{{$nextDays}}</div>
								@endforeach
								@endif
					</div>
				</div>
			</div>
		</div>
        <script>
            function app() {
                return {
                    month: '',
                    year: '',
                    no_of_days: [],
                    blankdays: [],
                    days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    
                    initDate() {
                        let today = new Date();
                        this.month = today.getMonth();
                        this.year = today.getFullYear();
                        this.datepickerValue = new Date(this.year, this.month, today.getDate()).toDateString();
                    },
                }
            }
        </script>
		</div>

		</div>
