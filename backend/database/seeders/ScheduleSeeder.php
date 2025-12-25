<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\SchedulePeriod;
use App\Models\User;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $SCHEDULE_DAYS = 14;
        $SCHEDULE_TYPES = ['foh', 'boh'];

        $startDate = now()->startOfWeek();

        // Schedule Period
        $period = SchedulePeriod::create([
            'start_date' => $startDate,
            'end_date' => $startDate->copy()->addDays(13),
            'is_current' => true,
        ]);

        // Schedules
        $workDate = today();
        for ($day = 1; $day <= $SCHEDULE_DAYS; $day++) {
            foreach ($SCHEDULE_TYPES as $type) {
                $schedule = Schedule::create([
                    'schedule_period_id' => $period->id,
                    'work_date' => $workDate->toDateString(),
                    'type' => $type,
                ]);

                // Shifts
                $this->createShifts($schedule);
            }

            $workDate = $workDate->addDay();
        }
    }

    private function createShifts(Schedule $schedule): void
    {
        $workDate = $schedule->work_date->format('Y-m-d');
        $scheduleType = $schedule->type;

        $shiftRequirements = [
            // Managers
            ['role' => 'mgr', 'count' => 1, 'time' => '10:00:00', 'types' => ['foh', 'boh']],
            ['role' => 'mgr', 'count' => 1, 'time' => '16:00:00', 'types' => ['foh', 'boh']],
            ['role' => 'mgr', 'count' => 1, 'time' => '14:00:00', 'types' => ['foh', 'boh']],

            // FOH
            ['role' => 'srv', 'count' => 1, 'time' => '10:00:00', 'types' => ['foh']],
            ['role' => 'srv', 'count' => 1, 'time' => '11:00:00', 'types' => ['foh']],
            ['role' => 'srv', 'count' => 1, 'time' => '12:00:00', 'types' => ['foh']],
            ['role' => 'hst', 'count' => 1, 'time' => '12:00:00', 'types' => ['foh']],
            ['role' => 'srv', 'count' => 7, 'time' => '17:00:00', 'types' => ['foh']],
            ['role' => 'hst', 'count' => 1, 'time' => '17:00:00', 'types' => ['foh']],

            // BOH
            ['role' => 'ktc', 'count' => 3, 'time' => '10:00:00', 'types' => ['boh']],
            ['role' => 'ktc', 'count' => 1, 'time' => '12:00:00', 'types' => ['boh']],
            ['role' => 'dsh', 'count' => 2, 'time' => '12:00:00', 'types' => ['boh']],
            ['role' => 'ktc', 'count' => 4, 'time' => '17:00:00', 'types' => ['boh']],
            ['role' => 'dsh', 'count' => 2, 'time' => '17:00:00', 'types' => ['boh']],
        ];

        $filteredReqs = array_filter($shiftRequirements, fn ($requirements) => in_array($scheduleType, $requirements['types']));

        $shifts = [];
        $employeeIndex = [];

        foreach ($filteredReqs as $requirement) {
            $roleCode = $requirement['role'];
            $count = $requirement['count'];
            $startTime = "$workDate {$requirement['time']}";

            $skip = $employeeIndex[$roleCode] ?? 0;

            $employees = User::whereHas('roles', function ($query) use ($roleCode) {
                $query->where('code', $roleCode);
            })->skip($skip)->limit($count)->get();

            foreach ($employees as $employee) {
                $shifts[] = [
                    'user_id' => $employee->id,
                    'work_date' => $workDate,
                    'shift_start_time' => $startTime,
                ];
            }

            $employeeIndex[$roleCode] = $skip + $count;
        }

        $schedule->shifts()->createMany($shifts);
    }
}
