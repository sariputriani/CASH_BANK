$rekapVA = [];

        if ($request->rekapanVA === 'bank') {

            $sdList = SumberDana::all();

            foreach ($sdList as $sd) {

                $transaksi = BankKeluar::where('id_sumber_dana', $sd->id_sumber_dana)
                                    ->orderBy('tanggal_keluar', 'asc')
                                    ->orderBy('created_at', 'asc')
                                    ->get();

                $saldo = 0;
                foreach ($transaksi as $t) {
                    $saldo += ($t->debet ?? 0) - ($t->kredit ?? 0);
                }

                $rekapVA[] = [
                    'bank'        => $sd->nama_sumber_dana,
                    'saldo_va'    => $saldo,
                    'saldo_sap'   => 0,
                    'selisih'     => $saldo,
                    'keterangan'  => 'Saldo akhir berdasarkan sumber dana'
                ];
            }
        }

        if ($request->rekapanVA === 'va') {

            $bankList = BankTujuan::all();

            foreach ($bankList as $bank) {

                $transaksi = BankKeluar::where('id_bank_tujuan', $bank->id_bank_tujuan)
                                    ->orderBy('tanggal_keluar', 'asc')
                                    ->orderBy('created_at', 'asc')
                                    ->get();

                // Hitung saldo akhir
                $saldo = 0;
                foreach ($transaksi as $t) {
                    $saldo += ($t->debet ?? 0) - ($t->kredit ?? 0);
                }

                $rekapVA[] = [
                    'bank'        => $bank->nama_tujuan,
                    'saldo_va'    => $saldo,   // tetap gunakan saldo_va agar Blade tidak perlu diubah
                    'saldo_sap'   => 0,
                    'selisih'     => $saldo,
                    'keterangan'  => 'Saldo akhir berdasarkan bank tujuan'
                ];
            }
        }


        // Data untuk dropdown filter
        $bankTujuanList = BankTujuan::all();
        $sumberDanaList = SumberDana::all();