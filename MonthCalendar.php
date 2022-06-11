namespace App\Http\Livewire;

use App\Models\Event;
use Livewire\Component;
use Illuminate\Support\Carbon;


class MonthCalendar extends Component
{
    public $events;
    //public $rule;
    public $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    public $month_names = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    public $dayNumber;
    public $displayMonth;
    public $year;
    public $today;
    public $selectedMonth;
    public $daysInMonth;
    public $dayOfWeek;
    public $previousDays;
    public $nextDays;
    public $no_of_days;

    public function mount()
    {
        $date = Carbon::now()->setTimezone('America/Chicago'); 
       // date_default_timezone_set('America/Chicago');
        $this->day = $date->format('d');
        $this->dayNumber = $date->format('j');
        $this->displayMonth = $date->format('F');
        $this->month = $date->format('m');
        $this->year = $date->format('Y');
        $this->today = Carbon::today()->setTimezone('America/Chicago')->format('Y-m-d');
        $this->selectedMonth = Carbon::today()->setTimezone('America/Chicago')->format('Y-m-01');
        
       // $this->events = Event::all();
        $this->getEvents($date);
        $this->getNoOfDays($this->month, $this->year);
    }

    public function nextMonth($month)
    {   
       // dd($month);
        $date = Carbon::createFromFormat('Y-m-d',  $month)->startOfMonth()->addMonth();
       // dd($date);
        $this->selectedMonth = $date->toDateString();
        
        //date_default_timezone_set('America/Chicago');
         
        //dd($date->format('Y-m-d'));
         $this->day = 1;
        if($this->today == $date){
            $this->dayNumber = date('j');
        }else{
            $this->dayNumber = null;
        }
        
         $this->month = $date->format('m');
         $this->displayMonth = $date->format('F');
         $this->year = $date->format('Y');
       
         $this->getEvents($date);
         $this->getNoOfDays($this->month, $this->year);
    }

    public function prevMonth($month)
    {   
        $date = Carbon::createFromFormat('Y-m-d',  $month)->startOfMonth()->subMonth();
       // dd($date);
        $this->selectedMonth = $date->toDateString();
        
        //date_default_timezone_set('America/Chicago');
         
        //dd($date->format('Y-m-d'));
         $this->day = 1;
        if($this->today == $date){
            $this->dayNumber = date('j');
        }else{
            $this->dayNumber = null;
        }
        
         $this->month = $date->format('m');
         $this->displayMonth = $date->format('F');
         $this->year = $date->format('Y');
       
         $this->getEvents($date);
         $this->getNoOfDays($this->month, $this->year);
    }


    
    public function getNoOfDays($month, $year) {
        //dd($month,$year);
        $num_days = date('t', strtotime($this->day . '-' . $month . '-' . $year));
        $num_days_last_month = date('j', strtotime('last day of previous month', strtotime($this->day . '-' . $month . '-' . $year)));
        $days = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];
        $first_day_of_week = array_search(date('D', strtotime($year . '-' . $month . '-1')), $days);

        
        
        $this->daysInMonth = date('t',strtotime($year.'-'.$month.'-'.'1'));
        // find where to start calendar day of week
        //let dayOfWeek = new Date(this.year, this.month).getDay();
         $dayOfWeek = date('w',strtotime($year.'-'.$month.'-'.'1'));
         //dd($dayOfWeek,$first_day_of_week);
         $prevArray = [];
         if($first_day_of_week != 0){
         for ($i = $first_day_of_week; $i > 0; $i--) {
            array_push($prevArray, $num_days_last_month-$i+1);
                   
        }}
       
       // dd($blankdaysArray);
        $daysArray = [];
        for ($i=1; $i <= $this->daysInMonth; $i++) {
            array_push($daysArray, $i);
        }

        $nextArray = [];
        for ($i = 1; $i <= (42-$num_days-max($first_day_of_week, 0)); $i++) {
            array_push($nextArray, $i);
        }
        
        $this->previousDays = $prevArray;
        $this->nextDays = $nextArray;
        $this->no_of_days = $daysArray;
       // dd( $this->blankdays,$this->no_of_days);
    }

    public function getEvents($from)
    {
        
        $firstDayofMonth = $from->startOfMonth()->toDateString();
       // dd($firstDayofPreviousMonth);
        $lastDayofMonth = $from->endOfMonth()->toDateString();
        $from = date($firstDayofMonth);
        $to = date($lastDayofMonth);
        $this->events = Event::whereBetween('start_date', [$from, $to])->get();
        //dd($this->events);
    }

    public function newEvent($date)
    {
       // dd($date);
        $newDate = trim($date,"'");
       // dd($this->selectedMonth);
        $selMonth=strtotime($this->selectedMonth);
        $month=date("m",$selMonth);
        $year=date("Y",$selMonth);
        //hours, minutes, seconds, month, day, year
        $time = mktime(7,0,0,$month,$newDate,$year);
        $date = date('l, F d, Y h:i K',$time);
        //$newStartDate = date('Y-m-d', strtotime($year . '-' . $month . '-' . $newDate));
        $this->emit('showNewEventFromCalendar', $date);
    }

    public function changeTitleEvent($event_id, $value)
    {
        //dd($event_id, $value);
        $event = Event::find($event_id);
        $event->update([
            'title' => $value
        ]);
    }
    
    public function editEvent($event_id)
    {
       // dd($event_id);
        $this->emit('showEditEventFromCalendar', $event_id);
    }

    public function deleteEvent($event_id)
    {
        //dd($event_id);
        $event = Event::find($event_id);
        $event->delete();
    }

    public function reorderEvent($event_id, $date)
    {
        $eventId = substr($event_id, -1);
        $newDate = trim($date,"'");
        $event = Event::find($eventId);
        //dd($event);
        $start = strtotime($event->start_date);
        $end = strtotime($event->end_date);
        
        $startHour= date('H',$start);
        $startMinutes= date('i',$start);
        
        $endHour= date('H',$end);
        $endMinutes= date('i',$end);
        
        $selMonth=strtotime($this->selectedMonth);
        $month=date("m",$selMonth);
        $year=date("Y",$selMonth);
        //hours, minutes, seconds, month, day, year
        
        $time = mktime($startHour,$startMinutes,0,$month,$newDate,$year);
        $date = date('Y-m-d H:i:s',$time);
        $endtime = mktime($endHour,$endMinutes,0,$month,$newDate,$year);
        $enddate = date('Y-m-d H:i:s',$endtime);
       
        $event->update(['start_date' => $date, 'end_date' => $enddate]);
        $date2 = Carbon::createFromFormat('Y-m-d', $this->selectedMonth);
        $this->getEvents($date2);
    }
    
    public function render()
    {
        return view('livewire.month-calendar');
    }
}
