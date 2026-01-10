<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Evaluation Form --}}
        <form wire:submit="save">
            @php
                $questions = App\Models\EvaluationScore::getQuestionsForEvaluator($this->type);
                $grouped = $this->groupQuestions($questions);
            @endphp

            {{-- Comprehensive Evaluation Table --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        {{-- Table Header --}}
                        <thead>
                            <tr>
                                <th style="border: 1px solid #9CA3AF; padding: 12px 16px; text-align: left; font-weight: bold; font-size: 18px;">
                                    Performance Indicators
                                </th>
                                <th style="border: 1px solid #9CA3AF; padding: 12px; text-align: center; font-weight: bold; width: 64px;">
                                    3
                                </th>
                                <th style="border: 1px solid #9CA3AF; padding: 12px; text-align: center; font-weight: bold; width: 64px;">
                                    2
                                </th>
                                <th style="border: 1px solid #9CA3AF; padding: 12px; text-align: center; font-weight: bold; width: 64px;">
                                    1
                                </th>
                                <th style="border: 1px solid #9CA3AF; padding: 12px; text-align: center; font-weight: bold; width: 64px;">
                                    0
                                </th>
                            </tr>
                        </thead>

                        {{-- Table Body --}}
                        <tbody>
                            @foreach($grouped as $domainName => $strands)
                                @if(!$loop->first)
                                    {{-- Spacing between domains --}}
                                    <tr>
                                        <td colspan="5" style="height: 20px; border: none; background: transparent;"></td>
                                    </tr>
                                @endif

                                {{-- Domain Header Row --}}
                                <tr>
                                    <td colspan="5" style="border: 1px solid #9CA3AF; padding: 16px;">
                                        <h4 style="font-size: 20px; font-weight: 700; color: #22C55E; margin: 0; line-height: 1.25; text-transform: uppercase; letter-spacing: 0.05em;">
                                            {{ $domainName }}
                                        </h4>
                                        <x-evaluation-domain-description :domain-name="$domainName" />
                                    </td>
                                </tr>

                                @foreach($strands as $strandName => $strandQuestions)
                                    {{-- Strand Header Row --}}
                                    <tr>
                                        <td colspan="5" style="border: 1px solid #9CA3AF; padding: 12px 16px;">
                                            <h6 style="font-size: 16px; font-weight: 600; color: #16A34A; margin: 0; line-height: 1.25; padding-left: 12px;">
                                                {{ $strandName }}
                                            </h6>
                                        </td>
                                    </tr>

                                    {{-- Questions for this Strand --}}
                                    @foreach($strandQuestions as $questionKey => $questionText)
                                        <tr>
                                            <td style="border: 1px solid #9CA3AF; padding: 16px; font-size: 14px;">
                                                {{ $questionText }}
                                            </td>
                                            @for($score = 3; $score >= 0; $score--)
                                                <td style="border: 1px solid #9CA3AF; padding: 12px; text-align: center;">
                                                    <input
                                                        type="radio"
                                                        name="{{ $questionKey }}"
                                                        value="{{ $score }}"
                                                        wire:model="data.{{ $questionKey }}"
                                                        style="width: 16px; height: 16px; accent-color: #22C55E;"
                                                        required
                                                        @if($this->isLocked) disabled @endif
                                                    >
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</x-filament-panels::page>
