Feature: Controlled file access (pass-thru, not direct access)

    Scenario:
        When I go to page "/photo/52/photo/original/test.jpg"
        Then I get a photo with dimensions 640x427

        When I go to page "/photo/52/photo/thumb/test.jpg"
        Then I get a photo with dimensions 300x200
