<x-filament-panels::page layout="top">
	<style>
		.evaluation-table {
			width: 100%;
			border-radius: 25%;
            border: 1px solid rgb(209 213 219);
		}
		.evaluation-table th, .evaluation-table td {
			padding: 0.75rem 1rem;
			text-align: left;
            font-size: 0.875rem;
			border-bottom: 1px solid rgb(209 213 219);
		}
		.evaluation-table th {
			font-weight: 600;
            color: #22c55e;
			font-size: 0.875rem;
			border-bottom: 1px solid rgb(209 213 219);
		}
		.evaluation-table tbody tr {
			border-bottom: 1px solid rgb(209 213 219);
		}
		.evaluation-table .evaluatee-name {
			font-weight: 400;
		}
		.evaluation-table .org-name {
			font-weight: 400;
		}
		.evaluation-avatar {
			width: 64px;
			height: 64px;
			border-radius: 50%;
			background: #f3f3f3;
			object-fit: cover;
			display: block;
		}
	</style>
		@if($tasks->isEmpty())
			<x-filament::card>
				<div class="flex items-center gap-4">
					<div class="flex-shrink-0" style="width:48px;height:48px;">
						<x-heroicon-o-clipboard-document-check class="text-gray-400" style="width:48px;height:48px;max-width:none;" />
						</div>
					<div>
						<h3 class="text-sm font-medium text-gray-900">No evaluations</h3>
						<p class="text-sm text-gray-500">You don't have any evaluation tasks assigned yet.</p>
					</div>
				</div>
			</x-filament::card>
		@else
			<div style="overflow-x:auto;">
				<table class="evaluation-table">
					<thead>
						<tr>
							<th>Evaluatee</th>
							<th>Organization</th>
							<th>Type</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach($tasks as $task)
						<tr>
							<td>
								<span class="evaluatee-name">{{ $task['target_name'] }}</span>
							</td>
							<td>
								<span class="org-name">{{ $task['organization_name'] }}</span>
							</td>
							<td>
								<x-filament::badge
									:color="$task['task_type'] === 'Self-Evaluation' ? 'info' : 'warning'"
									:icon="$task['task_type'] === 'Self-Evaluation' ? 'heroicon-s-user' : 'heroicon-s-users'">
									{{ $task['task_type'] }}
								</x-filament::badge>
							</td>
							<td>
								<x-filament::badge
									:color="$task['status'] === 'Completed' ? 'success' : 'warning'"
									:icon="$task['status'] === 'Completed' ? 'heroicon-s-check-circle' : 'heroicon-s-clock'">
									{{ $task['status'] }}
								</x-filament::badge>
							</td>
							<td>
								@if($task['task_type'] === 'Self-Evaluation')
									<x-filament::button
										tag="a"
										:href="route('filament.student.resources.evaluations.self-evaluate', ['evaluation' => $task['evaluation_id']])"
										size="sm"
										:icon="$task['status'] === 'Completed' ? 'heroicon-s-eye' : 'heroicon-s-pencil-square'">
										@if($task['status'] === 'Completed')
											View Evaluation
										@else
											Start Evaluation
										@endif
									</x-filament::button>
								@else
									<x-filament::button
										tag="a"
										:href="route('filament.student.resources.evaluations.peer-evaluate', ['evaluation' => $task['evaluation_id'], 'student' => $task['target_id']])"
										size="sm"
										:icon="$task['status'] === 'Completed' ? 'heroicon-s-eye' : 'heroicon-s-pencil-square'">
										@if($task['status'] === 'Completed')
											View Evaluation
										@else
											Start Evaluation
										@endif
									</x-filament::button>
								@endif
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</x-filament-panels::page>
