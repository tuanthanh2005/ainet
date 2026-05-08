class Setting extends Model {
    private static $dataFile = __DIR__ . '/../../storage/data/settings.json';

    public static function getAll() {
        if (file_exists(self::$dataFile)) {
            $json = file_get_contents(self::$dataFile);
            return json_decode($json, true);
        }
        return [
            "bannerText" => "DỊCH VỤ AI CAO CẤP - BẢO HÀNH 1 ĐỔI 1",
            "zalo" => "0772698113",
            "footerDesc" => "Hệ thống phân phối giải pháp phần mềm, tài khoản dịch vụ số tối giản, nhanh chóng và tinh tế nhất.",
            "socialLink" => "https://twitter.com/aicualtoi",
            "copyright" => "2026 AI CỦA TÔI"
        ];
    }

    public static function saveAll($data) {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents(self::$dataFile, $json);
    }
}
