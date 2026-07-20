<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\country-comparison\index.blade.php';
$content = file_get_contents($file);

$oldA = <<<'EOD'
                        @forelse($countryA->latestNews as $news)
                            <div class="mb-2 pb-2 border-bottom">
                                <p class="mb-1 fw-bold small text-truncate" title="{{ $news->title }}">{{ $news->title }}</p>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($news->published_at)->format('d M') }} &bull; <span class="{{ strtolower($news->sentiment) == 'positive' ? 'text-success' : (strtolower($news->sentiment) == 'negative' ? 'text-danger' : 'text-muted') }}">{{ $news->sentiment }}</span></small>
                            </div>
                        @empty
                            <small class="text-muted">No news available.</small>
                        @endforelse
EOD;

$newA = <<<'EOD'
                        @php 
                            $newsListA = (is_iterable($countryA->latestNews) && count($countryA->latestNews) > 0) ? $countryA->latestNews : [];
                            $hasValidNewsA = false;
                        @endphp
                        @forelse($newsListA as $news)
                            @if(is_object($news) && isset($news->title))
                                @php $hasValidNewsA = true; @endphp
                                <div class="mb-2 pb-2 border-bottom">
                                    <p class="mb-1 fw-bold small text-truncate" title="{{ $news->title }}">{{ $news->title }}</p>
                                    <small class="text-muted">{{ isset($news->published_at) ? \Carbon\Carbon::parse($news->published_at)->format('d M') : '-' }} &bull; <span class="{{ isset($news->sentiment) && strtolower($news->sentiment) == 'positive' ? 'text-success' : (isset($news->sentiment) && strtolower($news->sentiment) == 'negative' ? 'text-danger' : 'text-muted') }}">{{ $news->sentiment ?? 'Unknown' }}</span></small>
                                </div>
                            @endif
                        @empty
                            <small class="text-muted">No trade news available.</small>
                        @endforelse
                        @if(!$hasValidNewsA && count($newsListA) > 0)
                            <small class="text-muted">No trade news available.</small>
                        @endif
EOD;

$oldB = <<<'EOD'
                        @forelse($countryB->latestNews as $news)
                            <div class="mb-2 pb-2 border-bottom">
                                <p class="mb-1 fw-bold small text-truncate" title="{{ $news->title }}">{{ $news->title }}</p>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($news->published_at)->format('d M') }} &bull; <span class="{{ strtolower($news->sentiment) == 'positive' ? 'text-success' : (strtolower($news->sentiment) == 'negative' ? 'text-danger' : 'text-muted') }}">{{ $news->sentiment }}</span></small>
                            </div>
                        @empty
                            <small class="text-muted">No news available.</small>
                        @endforelse
EOD;

$newB = <<<'EOD'
                        @php 
                            $newsListB = (is_iterable($countryB->latestNews) && count($countryB->latestNews) > 0) ? $countryB->latestNews : [];
                            $hasValidNewsB = false;
                        @endphp
                        @forelse($newsListB as $news)
                            @if(is_object($news) && isset($news->title))
                                @php $hasValidNewsB = true; @endphp
                                <div class="mb-2 pb-2 border-bottom">
                                    <p class="mb-1 fw-bold small text-truncate" title="{{ $news->title }}">{{ $news->title }}</p>
                                    <small class="text-muted">{{ isset($news->published_at) ? \Carbon\Carbon::parse($news->published_at)->format('d M') : '-' }} &bull; <span class="{{ isset($news->sentiment) && strtolower($news->sentiment) == 'positive' ? 'text-success' : (isset($news->sentiment) && strtolower($news->sentiment) == 'negative' ? 'text-danger' : 'text-muted') }}">{{ $news->sentiment ?? 'Unknown' }}</span></small>
                                </div>
                            @endif
                        @empty
                            <small class="text-muted">No trade news available.</small>
                        @endforelse
                        @if(!$hasValidNewsB && count($newsListB) > 0)
                            <small class="text-muted">No trade news available.</small>
                        @endif
EOD;

$content = str_replace($oldA, $newA, $content);
$content = str_replace($oldB, $newB, $content);

file_put_contents($file, $content);
echo "News loops patched.\n";
