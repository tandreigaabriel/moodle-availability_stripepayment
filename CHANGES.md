# Changes

## 1.5.1 (2026-11-19)

- Prices with decimals are charged exactly: 9.99 was charged as 9.00 and recorded as 10. The amount column now stores two decimals (existing values are unchanged).
- Refunds are limited to site managers (`availability/stripepayment:managetransactions`); course teachers can still view the payments of their course.
- Free access can only be granted to users enrolled in the course.
- The totals on a course transactions report count that course only, and the course filter lists only the teacher's course.
- Activity and course names in the transactions report are formatted and escaped.
- The Stripe secret key and webhook secret are masked in the settings.
- The webhook answers with an error when processing fails, so Stripe retries the event.

## 1.5.0

- Refunds, manual access grants, payment events, transactions report updates.
