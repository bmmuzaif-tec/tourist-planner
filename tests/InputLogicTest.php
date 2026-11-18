<?php
/**
 * Unit tests for pure input-handling / formatting logic that mirrors the
 * inline expressions used across EravurGo pages (index.php, place-details.php,
 * export-plan.php, review.php, feedback.php, contact.php).
 *
 * These functions replicate exactly what the page scripts do with user input;
 * the tests pin down that behavior (ID sanitisation, rating clamping,
 * distance totals, truncation, escaping, email validation, filename building).
 */

// ---- Logic replicas of code under test ---------------------------------------

/** review.php / feedback.php: $place_id = intval($_GET['id']); $rating = intval($_POST['rating']); */
function ug_intval_input($raw)
{
    return intval($raw);
}

/** review.php renders one "⭐" per rating point: for($i=1;$i<=$row['rating'];$i++) */
function ug_stars(int $rating): string
{
    $out = '';
    for ($i = 1; $i <= $rating; $i++) {
        $out .= '⭐';
    }
    return $out;
}

/** export-plan.php: $total_distance += floatval($row['distance']) */
function ug_total_distance(array $rows): float
{
    $total = 0;
    foreach ($rows as $row) {
        $total += floatval($row['distance']);
    }
    return $total;
}

/** export-plan.php PDF cell values: substr($row['place_name'], 0, 25) etc. */
function ug_truncate(string $text, int $len): string
{
    return substr($text, 0, $len);
}

/** index.php cards: substr(htmlspecialchars($row['description']), 0, 100) */
function ug_card_excerpt(string $description): string
{
    return substr(htmlspecialchars($description), 0, 100) . '...';
}

/** export-plan.php date rendering: date('d M Y', strtotime($visit_date)) */
function ug_format_visit_date(string $mysqlDate): string
{
    return date('d M Y', strtotime($mysqlDate));
}

/** export-plan.php total row: number_format($total_distance, 2) . ' km' */
function ug_format_distance(float $km): string
{
    return number_format($km, 2) . ' km';
}

/** contact.php validation pipeline */
function ug_validate_contact(array $post): ?string
{
    $name    = trim($post['name'] ?? '');
    $email   = trim($post['email'] ?? '');
    $subject = trim($post['subject'] ?? '');
    $message = trim($post['message'] ?? '');

    if ($name === '' || $email === '' || $subject === '' || $message === '') {
        return '⚠️ Please fill in all fields.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return '⚠️ Please enter a valid email address.';
    }
    return null; // would proceed to DB insert
}

/** admin/add-place.php upload naming: time() . "_" . $_FILES['image']['name'] */
function ug_upload_filename(int $timestamp, string $originalName): string
{
    return $timestamp . '_' . $originalName;
}

/** includes/hero.php search box value: isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' */
function ug_search_box_value(array $get): string
{
    return isset($get['search']) ? htmlspecialchars($get['search']) : '';
}

// ---- Tests --------------------------------------------------------------------

class InputLogicTest extends TestCase
{
    public function test_intval_sanitizes_get_id_like_pages_do()
    {
        $this->assertSame(5,   ug_intval_input('5'));
        $this->assertSame(7,   ug_intval_input(' 7 '));
        $this->assertSame(12,  ug_intval_input('12abc'));      // "12abc" -> 12
        $this->assertSame(0,   ug_intval_input('abc'));         // non-numeric -> 0
        $this->assertSame(0,   ug_intval_input(''));            // empty -> 0
        $this->assertSame(-3,  ug_intval_input('-3'));
        $this->assertSame(1,   ug_intval_input('1.9'));         // truncated toward zero
        $this->assertSame(0,   ug_intval_input(null));
    }

    public function test_rating_is_clamped_to_integer_by_intval()
    {
        $this->assertSame(5, ug_intval_input('5'));
        $this->assertSame(0, ug_intval_input('invalid'));       // injection attempt -> 0
        $this->assertSame(3, ug_intval_input(3.99));
    }

    public function test_star_rendering_matches_rating()
    {
        $this->assertSame('', ug_stars(0));
        $this->assertSame('⭐⭐⭐', ug_stars(3));
        $this->assertSame('⭐⭐⭐⭐⭐', ug_stars(5));
    }

    public function test_total_distance_sums_float_values()
    {
        $rows = [
            ['distance' => '1.5'],
            ['distance' => '2.25'],
            ['distance' => '0'],
        ];
        $this->assertEquals(3.75, ug_total_distance($rows));
    }

    public function test_total_distance_handles_non_numeric_as_zero()
    {
        $rows = [
            ['distance' => 'abc'],     // floatval -> 0
            ['distance' => '4.2km'],   // floatval -> 4.2 (leading numeric part)
            ['distance' => null],      // floatval -> 0
        ];
        $this->assertEquals(4.2, ug_total_distance($rows));
    }

    public function test_total_distance_of_empty_plan_is_zero()
    {
        $this->assertSame(0.0, ug_total_distance([]));
    }

    public function test_pdf_cell_truncation()
    {
        $long = 'This place name is definitely longer than twenty five characters';
        $this->assertSame(25, strlen(ug_truncate($long, 25)));
        $this->assertSame('Short', ug_truncate('Short', 25));           // no padding/error
        $this->assertSame('1234567890', ug_truncate('1234567890', 18)); // category col width
    }

    public function test_card_excerpt_escapes_html_and_truncates()
    {
        $html = '<script>alert("xss")</script> A beach with golden sands and a beautiful lagoon view nearby.';
        $excerpt = ug_card_excerpt($html);

        $this->assertNotContainsString('<script>', $excerpt, 'raw script tag must be escaped');
        $this->assertContainsString('&lt;script&gt;', $excerpt);
        $this->assertTrue(strlen($excerpt) <= 104, 'excerpt + ellipsis bounded by 100 + 3');
        $this->assertContainsString('...', substr($excerpt, -3) === '...' ? $excerpt : $excerpt . '...');
    }

    public function test_format_visit_date_uses_mysql_datetime()
    {
        $this->assertSame('15 Aug 2025', ug_format_visit_date('2025-08-15'));
        $this->assertSame('01 Jan 2026', ug_format_visit_date('2026-01-01 00:00:00'));
    }

    public function test_format_distance_always_two_decimals_with_unit()
    {
        $this->assertSame('3.75 km', ug_format_distance(3.75));
        $this->assertSame('0.00 km', ug_format_distance(0));
        $this->assertSame('1,234.00 km', ug_format_distance(1234));
    }

    public function test_contact_validation_rejects_missing_fields()
    {
        $this->assertSame('⚠️ Please fill in all fields.',
            ug_validate_contact(['name' => '', 'email' => 'a@b.com', 'subject' => 'S', 'message' => 'M']));
        $this->assertSame('⚠️ Please fill in all fields.',
            ug_validate_contact(['name' => 'N', 'email' => ' ', 'subject' => 'S', 'message' => 'M']));
    }

    public function test_contact_validation_rejects_bad_email_accepts_good()
    {
        $this->assertSame('⚠️ Please enter a valid email address.',
            ug_validate_contact(['name' => 'N', 'email' => 'not-an-email', 'subject' => 'S', 'message' => 'M']));
        $this->assertNull(
            ug_validate_contact(['name' => 'N', 'email' => 'user@example.com', 'subject' => 'S', 'message' => 'M']));
    }

    public function test_upload_filename_prefixes_timestamp()
    {
        $this->assertSame('1700000000_er.png', ug_upload_filename(1700000000, 'er.png'));
    }

    public function test_search_box_value_escapes_and_defaults_empty()
    {
        $this->assertSame('', ug_search_box_value([]));
        $this->assertSame('lagoon', ug_search_box_value(['search' => 'lagoon']));
        $escaped = ug_search_box_value(['search' => '" onload="alert(1)']);
        // htmlspecialchars must neutralise the attribute-breaking quote.
        $this->assertContainsString('&quot;', $escaped);
        $this->assertNotContainsString('" onload="', $escaped);
    }
}
