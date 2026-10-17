<?php

namespace App\Support;

class SakEssayRubrics
{
    /** @return array<string, mixed>|null */
    public static function for(string $questionCode): ?array
    {
        return self::all()[$questionCode] ?? null;
    }

    /** @return array<string, array<string, mixed>> */
    public static function all(): array
    {
        return [
            'SAK-B01-Q010' => self::rubric([
                [2, ['smartwatch perangkat multifungsi', 'perangkat multifungsi', 'komunikasi nirkabel', 'transceiver']],
                [2, ['catatan 1 n bab 91', 'catatan 1n bab 91', 'bab 91 mengecualikan']],
                [2, ['kumhs 1']],
                [2, ['kumhs 3 b', 'karakter hakiki']],
                [2, ['8517 62 59', 'pos 85 17', 'bukan pos 91 02']],
            ]),
            'SAK-B02-Q010' => self::rubric([
                [1.5, ['22 000 000', '22000000']],
                [1.5, ['2 200 000 000', '2200000000']],
                [1.5, ['110 000 000', '110000000']],
                [1.5, ['2 310 000 000', '2310000000']],
                [1.5, ['254 100 000', '254100000']],
                [1.5, ['57 750 000', '57750000']],
                [1, ['421 850 000', '421850000']],
            ]),
            'SAK-TRYOUT-S1-Q001' => self::rubric([
                [10, ['perangkat terpadu', 'penerangan jalan surya', 'lampu led']],
                [10, ['catatan 3 bagian xvi', 'catatan 1 bagian xx', 'pos 94 05']],
                [10, ['kumhs 1'], ['kumhs 3 b']],
                [10, ['fungsi utama penerangan', 'karakter hakiki penerangan', 'panel surya dan baterai pemasok daya']],
                [10, ['9405 42 10', 'bukan pos 85 01', 'bukan pos 85 07']],
            ]),
            'SAK-TRYOUT-S1-Q002' => self::rubric([
                [10, ['32 500 000', '3 250 000 000', '812 500 000']],
                [10, ['35 000 000', '3 500 000 000', '875 000 000']],
                [10, ['62 500 000', '104 687 500', '7 69']],
                [10, ['pasal 16 ayat 4', 'denda 100', '62 500 000']],
                [10, ['167 187 500', '167187500']],
            ]),
        ];
    }

    /** @param array<int, array<int, mixed>> $criteria */
    private static function rubric(array $criteria): array
    {
        return [
            'mode' => 'keyword_rubric',
            'criteria' => array_map(static function (array $criterion): array {
                $requiredGroups = array_slice($criterion, 1);

                return [
                    'points' => (float) $criterion[0],
                    'required_groups' => array_map(
                        static fn (mixed $group): array => array_values((array) $group),
                        $requiredGroups
                    ),
                ];
            }, $criteria),
        ];
    }
}
