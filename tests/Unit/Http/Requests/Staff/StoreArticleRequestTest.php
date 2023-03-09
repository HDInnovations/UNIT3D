<?php

beforeEach(function (): void {
    $this->subject = new \App\Http\Requests\Staff\StoreArticleRequest();
});

test('authorize', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $actual = $this->subject->authorize();

    $this->assertTrue($actual);
});

test('rules', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $actual = $this->subject->rules();

    $this->assertValidationRules([
        'title'   => 'required|string|max:255',
        'content' => 'required|string|max:65536',
        'image'   => 'max:10240',
    ], $actual);
});

// test cases...
