<?php

use App\Models\DataIkan;

?>

<table style="width:100%" class="table table-bordered">
    <thead style="background: rgba(0, 0, 0, 0.1)">
        <tr>
            <th style="text-align:center" width="5%">No</th>
            <th style="text-align:center"> Nama Ikan / Barang </th>
            <th style="text-align:center" width="12%"> Jenis Produk </th>
            <th style="text-align:center" width="12%"> Berat (kg) </th>
            <th style="text-align:center" width="12%"> Jumlah Kemasan </th>
            <th style="text-align:center"> No. Segel </th>
            <th style="text-align:center"> Keterangan </th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($records as $key => $record) : ?>

            <tr>
                <td style="text-align: center" width="5%"><?php echo $key + 1; ?></td>
                <td style="text-align: center" width="20%"><?php echo  $record['nama_latin']; ?></td>
                <td style="text-align: center"><?php echo $record['produk']; ?> <?php echo $record['jenis_produk']; ?> <?php echo $record['kondisi_produk']; ?></td>
                <td style="text-align: center"><?php echo (($record['berat'] == '0.000') ? "" : $record['berat']); ?></td>
                <td style="text-align: center"><?php echo $record['kemasan'] . " " . $record['nama_satuan_barang']; ?></td>
                <td style="text-align: center" width="10%"><?php echo $record['no_segel']; ?> - <?php echo $record['no_segel_akhir']; ?></td>
                <td style="text-align: center"><?php echo $record['keterangan']; ?></td>
                
            </tr>
        <?php endforeach ?>
    </tbody>

    <tfoot>
        <tr>
            <td style="text-align: center; font-weight: bold" colspan="3">
                Total Berat:
            </td>
            <td style="text-align: center">
                <?=
                    array_reduce($records,
                        function ($curr, $next) {
                            return $curr + $next["berat"];
                        }
                    , 0)
                ?>
            </td>
            <td> </td>
            <td> </td>
            <td> </td>
        </tr>
    </tfoot>
</table>