<?php
$file = 'c:\laragon\www\global-trade-risk-dashboard\resources\views\reports\exports\country-comparison.blade.php';
$content = file_get_contents($file);

$oldTh = <<<'EOD'
                    <th>Risk Score B</th>
                    <th>Recommended</th>
                    <th>Date</th>
                </tr>
EOD;

$newTh = <<<'EOD'
                    <th>News Sent. A</th>
                    <th>News Sent. B</th>
                    <th>Risk Score A</th>
                    <th>Risk Score B</th>
                    <th>Recommended</th>
                    <th>Date</th>
                    <th>Created By</th>
                </tr>
EOD;

$oldTd = <<<'EOD'
                        <td>
                            @php
                                $rA = (float)$row['risk_score_a'];
                                $badgeA = $rA < 40 ? 'bg-success' : ($rA < 70 ? 'bg-warning' : 'bg-danger');
                            @endphp
                            <span class="badge {{ $badgeA }}">{{ $row['risk_score_a'] }}</span>
                        </td>
EOD;

$newTd = <<<'EOD'
                        <td>{{ number_format((float)$row['news_sentiment_a'], 2) }}</td>
                        <td>{{ number_format((float)$row['news_sentiment_b'], 2) }}</td>
                        <td>
                            @php
                                $rA = (float)$row['risk_score_a'];
                                $badgeA = $rA < 40 ? 'bg-success' : ($rA < 70 ? 'bg-warning' : 'bg-danger');
                            @endphp
                            <span class="badge {{ $badgeA }}">{{ $row['risk_score_a'] }}</span>
                        </td>
EOD;

$oldEndTd = <<<'EOD'
                        <td><strong>{{ $row['recommendation'] }}</strong></td>
                        <td>{{ $row['comparison_date'] }}</td>
                    </tr>
EOD;

$newEndTd = <<<'EOD'
                        <td><strong>{{ $row['recommendation'] }}</strong></td>
                        <td>{{ $row['comparison_date'] }}</td>
                        <td>{{ $row['created_by'] }}</td>
                    </tr>
EOD;

$content = str_replace($oldTh, $newTh, $content);
$content = str_replace($oldTd, $newTd, $content);
$content = str_replace($oldEndTd, $newEndTd, $content);
file_put_contents($file, $content);
echo "View updated.\n";
