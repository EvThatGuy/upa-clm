jQuery(document).ready(function($) {
    console.log('Admin scripts loaded');

    // Form Validation
    $('form').on('submit', function(event) {
        let isValid = true;
        $(this).find('input[required], select[required]').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).css('border-color', 'red');
            } else {
                $(this).css('border-color', '');
            }
        });

        if (!isValid) {
            event.preventDefault();
            alert('Please fill in all required fields.');
        }
    });

    // AJAX Call to Add a Team
    $('#add-team-form').on('submit', function(event) {
        event.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: ajaxurl, // WordPress's ajaxurl
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Team added successfully!');
                    location.reload(); // Reload the page to show the new team
                } else {
                    alert('Error adding team. Please try again.');
                }
            },
            error: function() {
                alert('AJAX request failed. Please try again.');
            }
        });
    });

    // Dynamic Content Update for Team Details
    $('#team-selector').on('change', function() {
        let teamId = $(this).val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_team_details',
                team_id: teamId
            },
            success: function(response) {
                if (response.success) {
                    $('#team-details').html(response.data);
                } else {
                    alert('Error fetching team details.');
                }
            },
            error: function() {
                alert('AJAX request failed. Please try again.');
            }
        });
    });

    // AJAX Call to Edit a Team
    $('#edit-team-form').on('submit', function(event) {
        event.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Team updated successfully!');
                    location.reload(); // Reload the page to show the updated team
                } else {
                    alert('Error updating team. Please try again.');
                }
            },
            error: function() {
                alert('AJAX request failed. Please try again.');
            }
        });
    });

    // AJAX Call to Delete a Team
    $(document).on('click', '.delete-team-button', function(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to delete this team?')) {
            let teamId = $(this).data('team-id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_team',
                    team_id: teamId
                },
                success: function(response) {
                    if (response.success) {
                        alert('Team deleted successfully!');
                        location.reload(); // Reload the page to remove the deleted team
                    } else {
                        alert('Error deleting team. Please try again.');
                    }
                },
                error: function() {
                    alert('AJAX request failed. Please try again.');
                }
            });
        }
    });

    // AJAX Call to Add a Game
    $('#add-game-form').on('submit', function(event) {
        event.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Game added successfully!');
                    location.reload(); // Reload the page to show the new game
                } else {
                    alert('Error adding game. Please try again.');
                }
            },
            error: function() {
                alert('AJAX request failed. Please try again.');
            }
        });
    });

    // AJAX Call to Edit a Game
    $('#edit-game-form').on('submit', function(event) {
        event.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Game updated successfully!');
                    location.reload(); // Reload the page to show the updated game
                } else {
                    alert('Error updating game. Please try again.');
                }
            },
            error: function() {
                alert('AJAX request failed. Please try again.');
            }
        });
    });

    // AJAX Call to Delete a Game
    $(document).on('click', '.delete-game-button', function(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to delete this game?')) {
            let gameId = $(this).data('game-id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_game',
                    game_id: gameId
                },
                success: function(response) {
                    if (response.success) {
                        alert('Game deleted successfully!');
                        location.reload(); // Reload the page to remove the deleted game
                    } else {
                        alert('Error deleting game. Please try again.');
                    }
                },
                error: function() {
                    alert('AJAX request failed. Please try again.');
                }
            });
        }
    });

    // AJAX Call to Add a Player
    $('#add-player-form').on('submit', function(event) {
        event.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Player added successfully!');
                    location.reload(); // Reload the page to show the new player
                } else {
                    alert('Error adding player. Please try again.');
                }
            },
            error: function() {
                alert('AJAX request failed. Please try again.');
            }
        });
    });

    // AJAX Call to Edit a Player
    $('#edit-player-form').on('submit', function(event) {
        event.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Player updated successfully!');
                    location.reload(); // Reload the page to show the updated player
                } else {
                    alert('Error updating player. Please try again.');
                }
            },
            error: function() {
                alert('AJAX request failed. Please try again.');
            }
        });
    });

    // AJAX Call to Delete a Player
    $(document).on('click', '.delete-player-button', function(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to delete this player?')) {
            let playerId = $(this).data('player-id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_player',
                    player_id: playerId
                },
                success: function(response) {
                    if (response.success) {
                        alert('Player deleted successfully!');
                        location.reload(); // Reload the page to remove the deleted player
                    } else {
                        alert('Error deleting player. Please try again.');
                    }
                },
                error: function() {
                    alert('AJAX request failed. Please try again.');
                }
            });
        }
    });
});