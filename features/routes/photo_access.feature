Feature: Controlled file access (pass-thru, not direct access)

    Scenario:
        When I go to page "/photo/52/photo/test.jpg"
        Then I get a photo with dimensions 640x427
