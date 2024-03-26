def find_values_divisible_by_n(num):
    """
    Function to find values between 3000 and 5000 that are divisible by the input number,
    the input number plus 7, or the square of the input number.
    
    Args:
    num (int): The input integer
    
    Returns:
    list: A list of values between 3000 and 5000 that meet the criteria
    """
    result = []
    for i in range(3000, 5001):
        # Check if the current value is divisible by num, num + 7, or num squared
        if i % num == 0 or i % (num + 7) == 0 or i % (num ** 2) == 0:
            result.append(i)
    return result

def main():
    """
    Main function to get user input, call the find_values_divisible_by_n function,
    and print the resulting list of values.
    """
    # Prompt the user to enter an integer
    num = int(input("Enter an integer: "))
    
    # Find values meeting the criteria and store them in values
    values = find_values_divisible_by_n(num)
    
    # Print the resulting list of values
    print(values)

if __name__ == "__main__":
    main()
