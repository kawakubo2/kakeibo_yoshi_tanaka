<?php
class Person {
    static string $SPECIES = "ホモサピエンス";
    // 属性またはプロパティ
    private string $firstName;
    private string $lastName;
    private float $height;
    private float $weight;
    // __で始まるメソッドを特殊メソッドまたはマジックメソッドと呼ぶ
    public function __construct(string $firstName, string $lastName, float $height, float $weight) 
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->height = $height;
        $this->weight = $weight;
    }
    // メソッドは属性に自由にアクセスできる
    /**
     * 姓(lastName)と名(firstName)を文字列結合して返す
     *
     * @return 結合した結果の姓名
     */
    public function getName() {
        return $this->lastName . $this->firstName;
    }
    /**
     * 身長(cm)と体重(kg)を元にBMI値を計算して返す
     *
     * @return void
     */
    public function bmi() {
        return $this->weight / pow($this->height / 100, 2);
    }
}